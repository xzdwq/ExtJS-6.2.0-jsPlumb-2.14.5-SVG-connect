Ext.define('Card.controller.Main', {
  extend: 'Ext.app.ViewController',
  alias: 'controller.main',
  init: function() {
    this.control({
      '#baseCard': {
        afterrender: this.onBaseCardinit
      },
      '[name=svgContainerControl]': {
        afterrender: this.onSvgContainerControl
      },
      '[name=svgDescriptionControl]': {
        afterrender: this.onSvgDescriptionControl
      }
    })
  },
  onBaseCardinit: function() {
    this.chekTotalCard();
    let cardInfo = this.onGetBaseCard();
    jsPlumbInstance.push(jsPlumb.getInstance({uuids: cardInfo.thisNumberInstance}));
    jsPlumbInstance[cardInfo.thisNumberInstance].importDefaults({
      Connector: ['Bezier', {
        curviness: 150
      }],
      Anchors: ['BottomCenter', 'TopCenter']
    });
  },
  onGetBaseCard: function() {
    let baseCard = Ext.ComponentQuery.query('#baseCard')[0];
    let baseLayout = baseCard.getLayout();
    let activCardId = baseLayout.getActiveItem().id;
    let activeCard = activCardId.split('card-')[1];
    let activeCardEl = Ext.ComponentQuery.query('#'+activCardId)[0];
    let totalCard = baseLayout.activeItemCount;
    let newCardNumber = totalCard + 1;
    let thisNumberInstance = activeCard;
    let getBaseCard = {
      baseCard: baseCard,
      baseLayout: baseLayout,
      activeCard: activeCard,
      activCardId: activCardId,
      countItemCard: activeCardEl.items.items.length,
      totalCard: totalCard,
      newCardNumber: newCardNumber,
      activeCardEl: activeCardEl,
      thisNumberInstance: thisNumberInstance
    }
    return getBaseCard;
  },
  onBack: function(el) {
    let cardInfo = this.onGetBaseCard();
    cardInfo.baseLayout.getPrev() ? cardInfo.baseLayout.prev() : null
    this.chekTotalCard();
  },
  onNext: function(el) {
    let cardInfo = this.onGetBaseCard();
    cardInfo.baseLayout.getNext() ? cardInfo.baseLayout.next(3) : null
    this.chekTotalCard();
  },
  chekTotalCard: function(el) {
    let cardInfo = this.onGetBaseCard();
    let prev = Ext.ComponentQuery.query('#cardPrev')[0];
    let next = Ext.ComponentQuery.query('#cardNext')[0];
    prev.setDisabled(cardInfo.activeCard == 1);
    next.setDisabled(cardInfo.activeCard == cardInfo.totalCard);
    let totalCardInfo = Ext.ComponentQuery.query('#totalCardInfo')[0];
    totalCardInfo.setValue(cardInfo.activeCard+' / '+cardInfo.totalCard)
  },
  onAddCard: function() {
    let cardInfo = this.onGetBaseCard();
    cardInfo.baseCard.add(
      {
        xtype: 'panel',
        id: 'card-' + cardInfo.newCardNumber,
        html: 'card-' + cardInfo.newCardNumber
      }
    );
    cardInfo.baseLayout.setActiveItem(cardInfo.totalCard);
    this.chekTotalCard();
    cardInfo = this.onGetBaseCard();
    jsPlumbInstance.push(jsPlumb.getInstance({uuids: cardInfo.thisNumberInstance}));
    jsPlumbInstance[cardInfo.thisNumberInstance].importDefaults({
      Connector: ['Bezier', {
        curviness: 150
      }],
      Anchors: ['BottomCenter', 'TopCenter']
    });
  },
  onDelCard: function() {
    let cardInfo = this.onGetBaseCard();
    if(cardInfo.totalCard > 1 && cardInfo.activeCard > 1) {
      this.onBack();
      cardInfo. baseCard.remove(cardInfo.activeCardEl);
      cardInfo.baseCard.updateLayout();
      this.chekTotalCard();
    } else if(cardInfo.totalCard >= 1 && cardInfo.activeCard == 1) { console.log('not del card') }
  },
  onAddSVG: function() {
    let cardInfo = this.onGetBaseCard();
    let svgGrid = '<table class="svgGrid">';
    for(let tr = 0; tr < 30; tr++) {
      svgGrid += '<tr class="tr-svgGrid tr-svgGrid-'+cardInfo.totalCard+'-'+tr+'">';
      for(let td = 0; td < 30; td++) {
        svgGrid += '<td class="td-svgGrid td-svgGrid-'+cardInfo.totalCard+'-'+td+'-'+tr+'"></td>';
      }
      svgGrid += '</tr>';
    }
    svgGrid += '</table>';
    cardInfo.activeCardEl.add(
      {
        xtype: 'svgContainer',
        cls: 'svgContainer-'+cardInfo.totalCard,
        itemId: 'svgContainer-'+cardInfo.totalCard,
        html: '<div class="svgDraw-'+cardInfo.totalCard+'"><svg><rect width="100%" height="100%" style="fill:'+this.generateRandomRGBA()+';stroke-width:40;stroke:rgb(102,102,102)" /></svg></div>'+svgGrid,
        x: 60, y: 60
      }
    )
  },
  generateRandomRGBA: function() {
    var o = Math.round, r = Math.random, s = 255;
    return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + r().toFixed(1) + ')';
  },
  onSvgContainerControl: function(el) {
    var me = this;
    this.source = new Ext.drag.Source({
      element: el.el,
      //handle: 'svg',
      proxy: 'original',
      constrain: {
        snap: {
          x: 30,
          y: 30
        }
      },
      listeners: {
        scope: me,
        dragstart: function() {},
        dragmove: function() {
          let cardInfo = me.onGetBaseCard();
          jsPlumbInstance[cardInfo.thisNumberInstance].repaintEverything();
        },
        dragend: function(source, info) {
          let pos = info.element.current;
          let getPos = Ext.String.format('X: {0}, Y: {1}', pos.x, pos.y);
          console.log(getPos);
        }
      }
    });
  },
  onAddDesc: function() {
    let cardInfo = this.onGetBaseCard();
    cardInfo.activeCardEl.add(
      {
        xtype: 'svgDescription',
        cls: 'svgDescription-'+cardInfo.activCardId,
        id: 'svgDescription-'+cardInfo.activCardId+'-'+cardInfo.countItemCard,
        x: 660, y: 270
      }
    );
    jsPlumbInstance[cardInfo.thisNumberInstance].connect({
      source: 'svgDescription-'+cardInfo.activCardId+'-'+cardInfo.countItemCard,
      target: $('.svgContainer-'+cardInfo.activeCard),
      scope: 'someScope'
    });
  },
  onSvgDescriptionControl: function(el) {
    var me = this;
    this.source = new Ext.drag.Source({
      element: el.el,
      handle: 'textarea',
      proxy: 'original',
      constrain: {
        snap: {
          x: 30,
          y: 30
        }
      },
      listeners: {
        scope: me,
        dragstart: function() {},
        dragmove: function(source, info, event, eOpts) {
          let cardInfo = me.onGetBaseCard();
          jsPlumbInstance[cardInfo.thisNumberInstance].repaintEverything();
        },
        dragend: function(source, info) {
          let pos = info.element.current;
          let getPos = Ext.String.format('X: {0}, Y: {1}', pos.x, pos.y);
          console.log(getPos);
        }
      }
    });
  },
  onRefreshCard: function() {
    let cardInfo = this.onGetBaseCard();
    while (cardInfo.activeCardEl.items.items[0]) {
      cardInfo.activeCardEl.remove(cardInfo.activeCardEl.items.items[0]);
    }
  },
  onCheckInsctance: function() {
    let cardInfo = this.onGetBaseCard();
    let allInstanceConnections = jsPlumbInstance[cardInfo.thisNumberInstance].getConnections({
      scope: 'someScope'
    });
    console.log('uuids_ist::'+jsPlumbInstance[cardInfo.thisNumberInstance].Defaults.uuids);
    // console.log(jsPlumbInstance[cardInfo.thisNumberInstance]);
    //console.log(allInstanceConnections);
  }
});
