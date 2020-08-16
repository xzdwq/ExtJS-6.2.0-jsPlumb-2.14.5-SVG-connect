var jsPlumbInstance = ['xzdwq'];
Ext.Loader.setConfig({
  enabled: true,
  disableCaching: false
});
Ext.application({
  name: 'Card',
  appFolder: 'app',
  autoCreateViewport: 'Card.view.Main',
  quickTips: false,
  platformConfig: {
    desktop: { quickTips: true }
  },
  launch: function() {
    Card.app = this;
  },
  init: function() {},
  initComponent: function() {
    this.callParent(arguments);
  }
});