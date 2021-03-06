/**
 * CashOnDelivery
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.cod.settings");
pimcore.plugin.cod.settings = Class.create({

    initialize: function () {
        pimcore.globalmanager.get("coreshop_carriers").load(this.getData.bind(this));

        //this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: "/plugin/CashOnDelivery/admin/get",
            success: function (response)
            {
                this.data = Ext.decode(response.responseText);

                this.getTabPanel();

            }.bind(this)
        });
    },

    getValue: function (key) {
        var current = null;

        if(this.data.values.hasOwnProperty(key)) {
            current = this.data.values[key];
        }

        if (current != "function") {
            return current;
        }

        return "";
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = Ext.create('Ext.panel.Panel', {
                id: "coreshop_cod",
                title: t("coreshop_cod"),
                iconCls: "coreshop_icon_cod",
                border: false,
                layout: "fit",
                closable:true
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("coreshop_cod");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("coreshop_cod");
            }.bind(this));


            var carrierTabs = [];
            var me = this;

            var carriers = pimcore.globalmanager.get("coreshop_carriers").getRange();

            Ext.each(carriers, function(carrier) {

                var tab = {
                    title: carrier.get("name"),
                    iconCls: "coreshop_icon_carriers",
                    items: [
                        {
                            xtype: "checkbox",
                            fieldLabel: t('coreshop_cod_active'),
                            name: 'COD.CARRIER.ACTIVE.' + carrier.get('id'),
                            checked: me.getValue('COD.CARRIER.ACTIVE.' + carrier.get('id'))
                        },
                        {
                            xtype: "numberfield",
                            fieldLabel: t('coreshop_cod_price'),
                            name: 'COD.CARRIER.PRICE.' + carrier.get('id'),
                            value: me.getValue('COD.CARRIER.PRICE.' + carrier.get('id')),
                            enableKeyEvents: true
                        },
                        {
                            xtype : 'multiselect',
                            name: 'COD.CARRIER.COUNTRIES.' + carrier.get('id'),
                            triggerAction: "all",
                            displayField:'name',
                            valueField:'id',
                            editable: false,
                            queryMode: 'local',
                            height : 200,
                            fieldLabel: t('coreshop_cod_countries'),
                            store: pimcore.globalmanager.get("coreshop_countries"),
                            value : me.getValue('COD.CARRIER.COUNTRIES.' + carrier.get('id'))
                        }
                    ]
                };

                carrierTabs.push( tab );
            });

            this.layout = Ext.create('Ext.form.Panel', {
                buttons: [
                    {
                        text: "Save",
                        handler: this.save.bind(this),
                        iconCls: "pimcore_icon_apply"
                    }
                ],
                items: [
                    {
                        xtype: "tabpanel",
                        activeTab: 0,
                        defaults: {
                            autoHeight:true,
                            bodyStyle:'padding:10px;'
                        },
                        items: carrierTabs
                    }
                ]
            });

            this.panel.add(this.layout);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.activate("coreshop_cod");
    },

    save: function () {
        var values = this.layout.getForm().getFieldValues();

        Ext.Ajax.request({
            url: "/plugin/CashOnDelivery/admin/set",
            method: "post",
            params: {
                data: Ext.encode(values)
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("coreshop_cod_save_success"), "success");
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("coreshop_cod_save_error"),
                            "error", t(res.message));
                    }
                } catch(e) {
                    pimcore.helpers.showNotification(t("error"), t("coreshop_cod_save_error"), "error");
                }
            }
        });
    }
});