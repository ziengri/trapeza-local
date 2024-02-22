class OrganizationByINNClass {
    _inputs = {
        inn: null,
        address: null,
        orgName: null,
        orgType: null,
        kpp: null,
        ogrn: null,
        okved: null,
    }

    _callbacks = {
        selected: [],
        inited: [],
        searchbefore: [],
        searchafter: [],
    }

    _setting = {
        inn: true,
        address: true,
        orgName: true,
        kpp: true,
        ogrn: true,
        orgType: true,
        branchType: true,
        okved: false
    }

    _findInputs = {
        inn: '[name="customf[inn][value]"], [name="f_inn"]',
        address: '[name="customf[address][value]"], [name="f_adres"]',
        orgName: 'input[name="f_company"], [name="customf[company][value]"]',
        orgType: '[name="customf[org][value]"], [name="f_org"]',
        kpp: '[name="f_kpp"], [name="customf[KPP][value]"]',
        okved: '[name="f_okved"], [name="customf[okved][value]"]',
    }

    resultBox = null

    dataVal = []

    constructor(form, setting = {}, findInputs = {}) {
        this.form = form;
        Object.assign(this._setting, setting);
        Object.assign(this._findInputs, findInputs);
        this.init()
    }

    init() {
        const id = "id" + Math.random().toString(16).slice(2);
        this.form.data('organization_id', `organization_${id}`).attr('data-organization_id', `organization_${id}`);
        window[`organization_${id}`] = this;

        for (const nameField in this._findInputs) {
            if (!this._inputs.hasOwnProperty(nameField)) continue;
            this._inputs[nameField] = this.form.find(this._findInputs[nameField])
        }

        if (!this._inputs.hasOwnProperty('inn') || !this._inputs.inn?.length) return false;
        if (!this.resultBox) this.resultBox = this._inputs.inn.parent('div');

        this._inputs.inn.on('change keyup', () => this.validateInn());

        const selectOrganization = this.selectOrganization;
        const self = this
        this.form.on('click', '.org-val', function () { selectOrganization($(this).data('id'), self); })
    }

    pushCallback(type, callback)
    {
        if (!this._callbacks.hasOwnProperty(type)) new Error(`Не верный ключ ${type}`);
        if (typeof callback != 'function') new Error(`Callback ${callback} не найден`);
        this._callbacks[type].push(callback);
    }

    validateInn() {
        const inn = this._inputs.inn
        const innValue = inn.val().replace(/\D/g, '')
        const resultBox = this.resultBox
        const getOrganization = this.getOrganization
        const self = this;

        if (innValue === inn.data('inn')) return;

        inn.data('inn', innValue);

        setTimeout(() => {
            if (inn.data('inn') !== innValue) return;

            if (innValue.length == 10 || innValue.length == 12) {
                resultBox?.removeClass('invalid')?.addClass('loading');
                getOrganization(innValue, self);
            } else {
                resultBox?.addClass('invalid');
            }
        }, 500);

        for (const callback of self._callbacks.searchbefore) {
            callback(self);
        }
    }

    createBoxResult(data) {
        this.resultBox?.removeClass('loading');
        if (data[0] == "{" || data[0] == "[") data = JSON.parse(data);


        const branchTypeValue = {
            MAIN: '',
            BRANCH: 'Филиал'
        }
        console.log(data);
        this.dataVal = [];
        if (!data?.suggestions || data.suggestions.length == 0) {
            this.dataVal.push({ orgName: 'Организация не найдена' })
        } else {
            for (const org of data.suggestions) {
                let orgParam = {
                    orgName: org.value,
                    address: org.data.address.unrestricted_value,
                    inn: org.data.inn,
                    type: org.data.opf.short,
                    orgType: org.data.ogrn,
                    okved: org.data.okved
                }
                if (org.data.kpp) orgParam.kpp = org.data.kpp;
                if (org.data.branch_type) orgParam.branch_type = branchTypeValue[org.data.branch_type];
                this.dataVal.push(orgParam);
            }

                console.log(this.dataVal);

        }

        const setting = this._setting;
        let orgValHtml = this.dataVal.reduce((acum, org, index) => {
            const orgName = (setting?.orgName && org.orgName ? `<span class="name"><b>Организация: </b>${org.orgName}${(setting?.branchType && org.branch_type ? ` (${org.branch_type})` : '')}</span>` : '');
            const address = (setting?.address && org.address ? `<span class="address"><b>Адрес: </b>${org.address}</span>` : '');
            const inn = (setting?.inn && org.inn ? `<span class="inn"><b>ИНН: </b>${org.inn}</span>` : '');
            const kpp = (setting?.kpp && org.kpp ? `<span class="kpp"><b>КПП: </b>${org.kpp}</span>` : '');

            acum += `<div class='org-val' data-id="${index}">
                        ${orgName}
                        ${address}
                        ${inn}
                        ${kpp}
                    </div>`;
            return acum;
        }, '')

        this.resultBox.find('#orgBox').remove();
        this.resultBox.append(`<div id="orgBox" style="left: ${this._inputs.inn.position().left}px; width: ${this._inputs.inn.innerWidth()}px;">${orgValHtml}</div>`);

        for (const callback of this._callbacks.searchafter) {
            callback(self);
        }
    }

    async getOrganization(inn, self) {
        const res = await $.post('/bc/modules/default/index.php?user_action=get_org_by_inn', { inn }, 'json');
        self.createBoxResult(res);
    }

    selectOrganization(id, self) {
        const paramOrg = self.dataVal[id];
        if (paramOrg.orgName == 'Организация не найдена') paramOrg.orgName = '';

        if (self._setting.orgName) {
            self._inputs.orgName?.val(paramOrg.orgName ?? '')?.attr('value', paramOrg.orgName ?? '')?.focus()
        }
        
        if (self._setting.kpp) {
            self._inputs.kpp?.val(paramOrg.kpp ?? '')?.attr('value', paramOrg.kpp ?? '')?.focus()
        }

        if (self._setting.address) {
            self._inputs.address?.val(paramOrg.address ?? '')?.attr('value', paramOrg.address ?? '')?.focus()
        }

        if (self._setting.okved) {
            self._inputs.okved?.val(paramOrg.okved ?? '')?.attr('value', paramOrg.okved ?? '')?.focus()
        }
    
        if (self._setting.ogrn) {
            self._inputs.ogrn?.val(paramOrg.ogrn ?? '')?.attr('value', paramOrg.ogrn ?? '')?.focus()
        }
       
        if (self._setting.orgType && paramOrg.orgType) {
            self._inputs.orgType.siblings(`.nice-select`).find(`.list-ul li[data-value="${paramOrg.orgType}"]`)?.click();
            self._inputs.orgType.find(`option`)?.each(function () {
                if ($(self).text() == paramOrg.orgType) $(self)?.prop('selected', true);
            });
        }

        self.resultBox.find('#orgBox').remove();

        for (const callback of self._callbacks.selected) {
            callback(self);
        }
    }

    getInput(name) {
        return this._inputs[name];
    }

}

