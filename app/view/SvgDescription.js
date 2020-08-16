Ext.define('Card.view.SvgDescription', {
  extend: 'Ext.panel.Panel',
  alias: 'widget.svgDescription',
  controller: 'main',
  name: 'svgDescriptionControl',
  layout: 'fit',
  frame: true,
  style: { position: 'absolute' },
  width: 180, height: 180,
  items: [
    {
      xtype: 'textarea',
      style: { position: 'absolute' }
    }
  ]
});