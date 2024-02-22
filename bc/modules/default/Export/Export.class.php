<?php

class Export
{
    public function __construct($data, $params = [])
    {
        global $setting;

        $this->paramsExport = $params;
        $this->data = $data;
        $this->setting = $setting;

        $this->main();
    }
    /**
     * @return void
     */
    public function main()
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        $this->nc_core = nc_Core::get_object();
        $this->db = $this->nc_core->db;
        $this->current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $this->catalogue =  $this->current_catalogue['Catalogue_ID'];
        $this->allSub = [];
        $this->normalazeSubs = [];
        $this->classNum = 2001;

        $this->rootSub = $this->db->get_row("   SELECT 
                                                    b.Subdivision_ID as sub, 
                                                    b.Sub_Class_ID as cc, 
                                                    b.Class_Template_ID as template, 
                                                    a.Hidden_URL 
                                                FROM 
                                                    Subdivision as a, 
                                                    Sub_Class as b 
                                                WHERE 
                                                    " . (isset($this->paramsExport['rootSub']) ? "a.Subdivision_ID = '{$this->paramsExport['rootSub']}'" : "a.Hidden_URL = '/catalog/'") . " AND 
                                                    a.Catalogue_ID = '{$this->catalogue}' AND 
                                                    a.Subdivision_ID = b.Subdivision_ID", 'ARRAY_A');

        $this->updateParamsItems();
        $this->updateSub();
        $this->updateItems();
    }
    public function updateParamsItems()
    {
        $this->paramsList = [];
        
        if (!$this->setting['lists_params']) {
            $this->setting['lists_params'] = [];
        }
        foreach ($this->setting['lists_params'] as $value) {
            $this->paramsList[$value['keyword']] = $value;
        }
        foreach ($this->data['params'] as $paramsID => $paramsName) {
            if (!$this->paramsList[$paramsID]) {
                $this->paramsList[$paramsID] = ["keyword" => $paramsID, "name" => $paramsName, "priority" => count($this->paramsList) + 1, 'checked' => 1];
            }
        }

        $this->setting['lists_params'] = array_values($this->paramsList);
        setSettings($this->setting);
    }

    public function updateSub()
    {
        $allSubDB = $this->db->get_results("SELECT 
                                        sub.Subdivision_ID as sub, 
                                        cc.Sub_Class_ID as cc, 
                                        sub.code1C, 
                                        sub.Hidden_URL
                                    FROM 
                                        Subdivision as sub, 
                                        Sub_Class as cc 
                                    WHERE 
                                        sub.Catalogue_ID = {$this->catalogue} AND 
                                        cc.Class_ID = 2001 AND 
                                        sub.Subdivision_ID = cc.Subdivision_ID AND 
                                        sub.code1C != ''", 'ARRAY_A');
        foreach ($allSubDB as $sub) {
            $this->allSub[$sub['code1C']] = ['sub' => $sub['sub'], 'cc' => $sub['cc'], 'Hidden_URL' => $sub['Hidden_URL'], 'update' => 0];
        }

        $this->createSub();
    }

    public function createSub()
    {
        $this->normolazeSub($this->data['groups']);

        $priority = count($this->allSub) + 1;
        foreach ($this->normalazeSubs as $sub) {
            $subID = $ccID = '';
            $idParent = ($sub['parentID'] == '' ? $this->rootSub['sub'] : $this->allSub[$sub['parentID']]['sub']);
            if (!$idParent) {
                continue;
            }

            $englishName = encodestring($sub['name'], 1);
            $Hidden_URL = ($sub['parentID'] == '' ? $this->rootSub['Hidden_URL'] . $englishName . '/' : $this->allSub[$sub['parentID']]['Hidden_URL']  . $englishName . '/');

            if (!$this->allSub[$sub['ID']]) {
                $sql = "INSERT INTO Subdivision
                                (   Catalogue_ID,
                                    Parent_Sub_ID,
                                    Subdivision_Name, 
                                    Priority, 
                                    Checked, 
                                    EnglishName, 
                                    Hidden_URL, 
                                    code1C, 
                                    subdir
                                ) 
                            VALUES
                                (   '{$this->catalogue}', 
                                    '{$idParent}', 
                                    '" . addslashes($sub['name']) . "', 
                                    '{$priority}', 
                                    '1', 
                                    '{$englishName}', 
                                    '{$Hidden_URL}', 
                                    '{$sub['ID']}', 
                                    3
                                )";
                $this->db->query($sql);
                $subID = $this->db->insert_id;

                if ($subID) {
                    $sql = "INSERT INTO Sub_Class 
                            (   Subdivision_ID, 
                                Class_ID, 
                                Sub_Class_Name, 
                                EnglishName, 
                                Checked, 
                                Class_Template_ID, 
                                Catalogue_ID, 
                                DefaultAction, 
                                AllowTags, 
                                NL2BR, 
                                UseCaptcha, 
                                CacheForUser, 
                                RecordsPerPage
                            ) 
                        VALUES
                            (   '{$subID}', 
                                {$this->classNum}, 
                                '{$sub['ID']}', 
                                '{$englishName}', 
                                1, 
                                '{$this->rootSub['template']}', 
                                {$this->catalogue}, 
                                'index', 
                                '-1', 
                                '-1', 
                                '-1', 
                                '-1', 
                                '50'
                            )";
                    $this->db->query($sql);
                    $ccID = $this->db->insert_id;
                }
            } else {
                $this->db->query("  UPDATE 
                                        Subdivision 
                                    SET 
                                        Checked = 1,
                                        Parent_Sub_ID = '{$idParent}',
                                        Hidden_URL = '{$Hidden_URL}' 
                                    WHERE 
                                        Subdivision_ID = '{$this->allSub[$sub['ID']]['sub']}'");
                $subID = $this->allSub[$sub['ID']]['sub'];
                $ccID = $this->allSub[$sub['ID']]['cc'];
            }

            if ($subID && $ccID) {
                $this->allSub[$sub['ID']] = ['sub' => $subID, 'cc' => $ccID, 'Hidden_URL' => $Hidden_URL, 'update' => 1];
            }
            echo '-s-';
            flush();
        }
        $disableSub = array_filter(array_column($this->allSub, 'update', 'sub'), function ($up) {
            return $up == 0;
        });

        $this->db->query("UPDATE Subdivision SET Checked = 0 WHERE Subdivision_ID IN (" . implode(',', array_keys($disableSub)) . ")");
    }

    public function updateItems()
    {
        foreach ($this->data['items'] as $id => $item) {
            $Keyword = encodestring(trim($item['name']) . " " . trim($item['art']), 1);
            $Subdivision_ID = $this->allSub[$item['sub']]['sub'];
            $Sub_Class_ID = $this->allSub[$item['sub']]['cc'];
            $this->db->query("INSERT INTO Message2001 
                            (   
                                Subdivision_ID, 
                                Sub_Class_ID,
                                Catalogue_ID, 
                                Keyword, 
                                Checked, 
                                name, 
                                text, 
                                stock, 
                                art, 
                                code, 
                                ves, 
                                edizm, 
                                descr, 
                                price,
                                photourl,
                                params,
                                timestamp_export
                            ) 
                        VALUES 
                            (
                                '{$Subdivision_ID}',
                                '{$Sub_Class_ID}',
                                '{$this->catalogue}',
                                '{$Keyword}',
                                '1',
                                '{$item['name']}',
                                '{$item['text']}',
                                '{$item['stock']}',
                                '{$item['art']}',
                                '{$id}',
                                '{$item['ves']}',
                                '{$item['edizm']}',
                                '{$item['descr']}',
                                '{$item['price']}',
                                '" . implode(',', $item['img']) . "',
                                '{$item['params']}',
                                '{$this->paramsExport['time']}'
                            ) ON DUPLICATE KEY UPDATE Checked = VALUES(Checked), price = VALUES(price), stock = VALUES(stock), photourl = VALUES(photourl), Subdivision_ID = VALUES(Subdivision_ID), Sub_Class_ID = VALUES(Sub_Class_ID), params = VALUES(params), timestamp_export = VALUES(timestamp_export)");
            echo '-i-';
            flush();
            ob_flush();
        }

        $this->db->query("UPDATE Message2001 SET Checked = 0 WHERE Catalogue_ID = '{$this->catalogue}' AND code != '' AND timestamp_export != '{$this->paramsExport['time']}'");
    }

    public function normolazeSub($subs, $priory = 0)
    {
        foreach ($subs as $key => $sub) {
            if (isset($this->normalazeSubs[$sub['parentID']]) || $sub['parentID'] == '') {
                $this->normalazeSubs[$sub['ID']] = $sub;
                unset($subs[$key]);
                $priory++;
            }
        }

        if (count($subs) > 0) {
            $this->normolazeSub($subs, $priory);
        }
    }
}
