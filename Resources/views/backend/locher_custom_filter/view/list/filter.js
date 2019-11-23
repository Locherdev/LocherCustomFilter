// {namespace name="backend/extend_filter/snippets"}

//{block name="backend/order/view/list/filter"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.LocherCustomFilter.view.list.Filter', {
    override: 'Shopware.apps.Order.view.list.Filter',

    /**
     * Contains all snippets for the view component
     * @object
     */
    snippets:{
        title:'{s name=filter/title}Filter options{/s}',
        from: '{s name=filter/from}From{/s}',
        to: '{s name=filter/to}To{/s}',
        orderState: '{s name=filter/orderState}Order status{/s}',
        paymentState: '{s name=filter/paymentState}Payment status{/s}',
        paymentName: '{s name=filter/paymentName}Payment method{/s}',
        dispatchName: '{s name=filter/dispatchName}Shipping type{/s}',
        customerGroup: '{s name=filter/customerGroup}Customer group{/s}',
        shop: '{s name=filter/shop}Shop{/s}',
        perform: '{s name=filter/perform}Perform filters{/s}',
        reset: '{s name=filter/reset}Reset filters{/s}',
        empty: '{s name=filter/empty}Display all{/s}',
        article: '{s name=filter/article}Article{/s}',
        partner: '{s name=filter/partner}Partner{/s}',
        shipping: '{s name=filter/shipping}Shipping country{/s}',
        billing: '{s name=filter/billing}Billing country{/s}',
        filter: '{s name=filter/customFilter}Custom filter{/s}',
        filterComplete: '{s name=filter/customFilter/complete}Only Fertig-PCs{/s}',
        filterIndividual: '{s name=filter/customFilter/individual}Only Indi-PCs{/s}',
        filterPiece: '{s name=filter/customFilter/piece}Only Einzelteile{/s}',
        document: {
            title: '{s name=document/title}Documents{/s}',
            date: '{s name=document/date}Date{/s}',
            name:  '{s name=document/name}Name{/s}'
        }
    },

    /**
     * Creates the form filter fields which displayed on the left hand of
     * the order list. The filters will be perform by the "Perform filters" button
     * which displayed under the form.
     * @return [Ext.form.Panel]
     */
    createFilterForm: function() {
        var me = this;

        me.filterForm = Ext.create('Ext.form.Panel', {
            border: false,
            cls: Ext.baseCSSPrefix + 'filter-form',
            defaults:{
                anchor:'98%',
                labelWidth:155,
                minWidth:250,
                xtype:'pagingcombo',
                style: 'box-shadow: none;',
                labelStyle: 'font-weight:700;'
            },
            items: [
                me.createFromField(),
                me.createToField(),
                me.createOrderStatusField(),
                me.createPaymentStatusField(),
                me.createPaymentField(),
                me.createDispatchField(),
                me.createCustomerGroupField(),
                me.createArticleSearch(),
                me.createShopField(),
                me.createPartnerField(),
                me.createDeliveryCountrySelection(),
                me.createBillingCountrySelection(),
                me.createCustomFilterSelection()
            ]
        });
        return me.filterForm;
    },

    createCustomFilterSelection: function() {
        var me = this;

        return Ext.create('Ext.form.field.ComboBox', {
            name: 'customFilter',
            displayField: 'name',
            valueField: 'name',
            store: me.createFilterSelection(),
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.filter
        });
    },

    createFilterSelection: function() {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields: [
                { name: 'name' }
            ],
            data: [
                { name: me.snippets.filterComplete },
                { name: me.snippets.filterIndividual },
                { name: me.snippets.filterPiece }
            ]
        });
    },
});
//{/block}