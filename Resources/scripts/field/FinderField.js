Ext.provide('Phlexible.elementfinder.field.FinderField');
Ext.require('Phlexible.elementfinder.ElementFinderConfigWindow');

Phlexible.elementfinder.field.FinderField = Ext.extend(Ext.form.TwinTriggerField, {
    trigger1Class: 'x-form-clear-trigger',
    trigger2Class: 'p-form-finder-trigger',
    hiddenValue: '',
    hideTrigger1: true,

    initComponent: function () {
        if (this.element) {
            this.siteroot_id = this.element.siteroot_id;
            this.language = this.element.language;
        }

        if (this.readonly) {
            this.editable = false;
            this.readOnly = true;
            this.hideTrigger = true;
            this.ctCls = 'x-item-disabled';
        }

        Phlexible.elementfinder.field.FinderField.superclass.initComponent.call(this);
    },

    onRender: function(ct, position) {
        Phlexible.elementfinder.field.FinderField.superclass.onRender.call(this, ct, position);

        this.hiddenField = Ext.DomHelper.insertAfter(this.el, {
            tag: 'input',
            name: this.hiddenName,
            type: 'hidden',
            value: Ext.encode(this.hiddenValue)
        }, true);
    },

    /**
     * Clears any text/value currently set in the field
     */
    clearValue : function(){
        if (this.hiddenField){
            this.setHiddenValue(null);
        }
        this.setRawValue('');
        this.lastSelectionText = '';
        this.applyEmptyText();
        this.value = '';
    },

    reset: Ext.form.Field.prototype.reset.createSequence(function () {
        this.triggers[0].hide();
    }),

    onViewClick: Ext.form.ComboBox.prototype.onViewClick.createSequence(function () {
        this.triggers[0].show();
    }),

    onTrigger2Click: function () {
        this.onTriggerClick();
    },

    onTrigger1Click: function () {
        if (this.disabled) {
            return;
        }
        this.clearValue();
        this.triggers[0].hide();
        this.onClear();
        this.fireEvent('clear', this);
    },

    initValue: function() {
        Phlexible.elementfinder.field.FinderField.superclass.initValue.call(this);

        this.setHiddenFieldValue(this.hiddenValue);
    },

    setHiddenValue: function(value) {
        this.hiddenValue = value;
        this.setHiddenFieldValue(value);
    },

    setHiddenFieldValue: function(value) {
        if (this.hiddenField) {
            if (value) {
                this.hiddenField.dom.value = Ext.encode(value);
            } else {
                this.hiddenField.dom.value = '';
            }
        }
    },

    setValue: function(v) {
        if (v && !this.readOnly) {
            this.hideTrigger1 = false;

            if (this.triggers) {
                this.triggers[0].show();
            }
        }

        Phlexible.elementfinder.field.FinderField.superclass.setValue.call(this, v);

        this.setHiddenFieldValue(this.hiddenValue);
    },

    validateValue: function (value) {
        return true;
    },

    onSelect: function (record, index) {
        Phlexible.elementfinder.field.FinderField.superclass.onSelect.call(this, record, index);

        this.setHiddenValue({
            type: record.data.type,
            tid: record.data.tid,
            eid: record.data.eid
        });
    },

    onClear: function () {
        this.setValue(null);
        this.setRawValue('');
        this.hiddenValue = this.getValue();
    },

    onTriggerClick: function (e, el) {
        if (this.disabled || this.readonly) {
            return;
        }

        var w = new Phlexible.elementfinder.ElementFinderConfigWindow({
            siterootId: this.siterootId,
            values: this.hiddenValue,
            baseValues: this.baseValues,
            language: this.language,
            listeners: {
                set: function(w, values) {
                    this.setValue(Ext.encode(values));
                    this.setRawValue('configured');
                    this.setHiddenValue(values);
                },
                scope: this
            }
        });
        w.show(el);
    }
});

Ext.reg('finderfield', Phlexible.elementfinder.field.FinderField);