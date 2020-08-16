Ext.define('Card.view.PanelSetting', {
  extend: 'Ext.panel.Panel',
  alias: 'widget.panelSetting',
  controller: 'main',
  layout: {
    type: 'vbox',
    align: 'stretch'
  },
  title: 'panelSetting',
  border: true,
  width: 150,
  bodyPadding: 5,
  defaults: { xtype: 'button', style:'margin-bottom: 5px;' },
  items: [
    {
      text: 'AddCard',
      cls: 'addCard',
      handler: 'onAddCard'
    },
    {
      text: 'DelCard',
      cls: 'delCard',
      handler: 'onDelCard'
    },
    {
      text: 'AddSVG',
      cls: 'addSVG',
      handler: 'onAddSVG'
    },
    {
      text: 'AddDesc',
      cls: 'addDesc',
      handler: 'onAddDesc'
    },
    {
      text: 'RefreshCard',
      cls: 'refreshCard',
      handler: 'onRefreshCard'
    },
    {
      text: 'CheckInsctance',
      cls: 'checkInsctance',
      handler: 'onCheckInsctance'
    }
  ]
});