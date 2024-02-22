<?php

namespace App\modules\Korzilla\Subdivision\Tasks;

use App\modules\Korzilla\Subdivision\Contracts\SubdivisionSortTaskContract;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;
use App\modules\Ship\Parent\Tasks\Task;

class SubdivisionSortTask extends Task implements SubdivisionSortTaskContract
{

    /**
     * Undocumented function
     *
     * @param SubdivisionSetInput[] $input
     * @return SubdivisionSetInput[]
     */
    public function run(array $input) : array
    {

        // Строим и сортируем дерево подразделений
        $sortedSubdivisions = $this->buildTree($input);

        // Сортируем массив по уровню вложенности
        usort($sortedSubdivisions, function(SubdivisionSetInput $a, SubdivisionSetInput $b) {
            return $a->level - $b->level;
        });



        return $sortedSubdivisions;
    }

    /**
     * @param SubdivisionSetInput[] $subdivisions
     * @param int|null $parentId
     * @return SubdivisionSetInput[]
     */
    private function getChildren(array $subdivisions,string $parentId = null) {

        $children = [];
        foreach ($subdivisions as $subdivision) {
            if ($subdivision->parentId === $parentId) {
                $children[] = $subdivision;
            }
        }
        return $children;
    }
    

    /**
     * Функция для рекурсивного построения и сортировки дерева подразделений
     *
     * @param SubdivisionSetInput[] $subdivisions
     * @param int|null $parentId
     * @param int $level
     * @return SubdivisionSetInput[]
     */
    private function buildTree(array $subdivisions, string $parentId = null, int $level = 0) {
        $tree = [];
        $children = $this->getChildren($subdivisions, $parentId);
        // var_dump($children);
        foreach ($children as $child) {
            $child->level = $level;
            $tree[] = $child;
            $tree = array_merge($tree, $this->buildTree($subdivisions, $child->id, ($level + 1) ));
        }
        return $tree;
    }
}





