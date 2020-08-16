Ext.define('Card.view.Main', {
  extend: 'Ext.panel.Panel',
  alias: 'widget.main',
  requires: [
    'Card.controller.Main',
    'Card.view.PanelSetting',
    'Card.view.SvgContainer',
    'Card.view.SvgDescription'
  ],
  controller: 'main',
  layout: 'border',
  cls: 'basePanel',
  border: true,
  items: [
    {
      xtype: 'panelSetting',
      region: 'east'
    },
    {
      xtype: 'panel',
      region: 'center',
      title: 'baseCard',
      itemId: 'baseCard',
      cls: 'baseCard',
      layout: {
        type: 'card',
        pack: 'start',
        align: 'stretch',
        animation: 'slide',
        deferredRender: true
      },
      defaults: { cls: 'cardStyle' },
      bodyPadding: 2,
      items: [
        {
          xtype: 'panel',
          id: 'card-1',
          html: 'card-1'
        }
      ],
      bbar: [
        {
          xtype: 'displayfield',
          itemId: 'totalCardInfo',
          cls: 'totalCardInfo',
          value: '0 / 0'
        },
        '->',
        {
            itemId: 'cardPrev',
            text: '&laquo; Previous',
            handler: 'onBack'
        },
        {
            itemId: 'cardNext',
            text: 'Next &raquo;',
            handler: 'onNext'
        }
      ]
    }
  ]
});