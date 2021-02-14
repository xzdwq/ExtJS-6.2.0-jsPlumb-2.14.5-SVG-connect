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
    console.log(cardInfo.activeCardId)
    jsPlumbInstance.push(jsPlumb.getInstance({uuids: cardInfo.thisNumberInstance, Container: cardInfo.activeCardId}));
    jsPlumbInstance[cardInfo.thisNumberInstance].importDefaults(cardInfo.defaultsStyle);
    jsPlumbInstance[cardInfo.thisNumberInstance].bind('click', function (connection, e) {
      jsPlumbInstance[cardInfo.thisNumberInstance].deleteConnection(connection);
    });
  },
  onGetBaseCard: function() {
    let defaultsStyle = {
      Connector: ['Flowchart', {
        curviness: 50
      }],
      Anchor: 'AutoDefault',
      Endpoints : [[ 'Dot', { radius: 3 } ], [ 'Rectangle', { radius: 3 } ]],
      EndpointStyles : [{ fill: '#225588' }, { fill: '#558822' }],
      ConnectionOverlays: [
        ['Arrow', {
          location: 1,
          id: 'arrow',
          length: 20,
          foldback: .8
        }]
      ],
      PaintStyle : {
        strokeWidth: 2,
        stroke: '#225588'
      },
      HoverPaintStyle: {
        strokeWidth: 3,
        strokeStyle: '#1e8151',
        lineWidth: 2
      }
    };
    let baseCard = Ext.ComponentQuery.query('#baseCard')[0];
    let baseLayout = baseCard.getLayout();
    let activeCardId = baseLayout.getActiveItem().id;
    let activeCard = activeCardId.split('card-')[1];
    let activeCardEl = Ext.ComponentQuery.query('#'+activeCardId)[0];
    let totalCard = baseLayout.activeItemCount;
    let newCardNumber = totalCard + 1;
    let thisNumberInstance = activeCard;
    let getBaseCard = {
      baseCard: baseCard,
      baseLayout: baseLayout,
      activeCard: activeCard,
      activeCardId: activeCardId,
      countItemCard: activeCardEl.items.items.length,
      totalCard: totalCard,
      newCardNumber: newCardNumber,
      activeCardEl: activeCardEl,
      thisNumberInstance: thisNumberInstance,
      defaultsStyle: defaultsStyle
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
    jsPlumbInstance.push(jsPlumb.getInstance({uuids: cardInfo.thisNumberInstance, Container: cardInfo.activeCardId}));
    jsPlumbInstance[cardInfo.thisNumberInstance].importDefaults(cardInfo.defaultsStyle);
    console.log(jsPlumbInstance)
  },
  onDelCard: function() {
    let cardInfo = this.onGetBaseCard();
    if(cardInfo.totalCard > 1 && cardInfo.activeCard > 1) {
      let allInstanceConnections = jsPlumbInstance[cardInfo.thisNumberInstance].getConnections({
        scope: 'someScope'
      });
      if (allInstanceConnections.length > 0) {
        for(var i=0; i < allInstanceConnections.length; i++) {
          jsPlumbInstance[cardInfo.thisNumberInstance].deleteConnection(allInstanceConnections[i]);
        }
        jsPlumbInstance[cardInfo.thisNumberInstance].deleteEveryEndpoint();
        jsPlumbInstance[cardInfo.thisNumberInstance].remove($('.svgDescription-'+cardInfo.activeCard));
        console.log(jsPlumbInstance[cardInfo.thisNumberInstance].getManagedElements())
      }
      jsPlumbInstance.splice(cardInfo.thisNumberInstance, cardInfo.thisNumberInstance);
      /* Проблема с удалением не последнего (+- из середины) слайда.
        Т.к. удаляется jsPlumbInstance из массива, то при перерисовки соединений
        в dragmove начинает ссыдаться не на тот инстанс из массива и бьет ошибку.
        Нужно как то получать текущий слайд и его номер передавать в jsPlumbInstance[].
      */
      this.onBack();
      cardInfo.baseCard.remove(cardInfo.activeCardEl);
      cardInfo.baseCard.updateLayout();
      this.chekTotalCard();
    } else if(cardInfo.totalCard >= 1 && cardInfo.activeCard == 1) { console.log('not del card') }
  },
  onAddSVG: function() {
    let cardInfo = this.onGetBaseCard();
    Ext.Ajax.request({
      url: 'img/svg.json',
      success: function(response, opts) {
        let xml = response.responseText;

        let svgGrid = '<table class="svgGrid">';
        for(let tr = 0; tr < 30; tr++) {
          svgGrid += '<tr class="tr-svgGrid tr-svgGrid-'+cardInfo.activeCard+'-'+tr+'">';
          for(let td = 0; td < 30; td++) {
            svgGrid += '<td class="td-svgGrid td-svgGrid-'+cardInfo.activeCard+'-'+td+'-'+tr+'"></td>';
          }
          svgGrid += '</tr>';
        }
        svgGrid += '</table>';
        cardInfo.activeCardEl.add(
          {
            xtype: 'svgContainer',
            cls: 'svgContainer-'+cardInfo.activeCard,
            itemId: 'svgContainer-'+cardInfo.activeCard,
            //html: '<div class="svgDraw-'+cardInfo.activeCard+'"><svg><rect width="100%" height="100%" style="fill:'+this.generateRandomRGBA()+';stroke-width:40;stroke:rgb(102,102,102)" /></svg></div>'+svgGrid,
            html: '<div id="svgDraw-'+cardInfo.activeCard+'" class="svgDraw-'+cardInfo.activeCard+'">'+xml+'</div>'+svgGrid,
            x: 60, y: 60
          }
        );
        let grid = Ext.ComponentQuery.query('#'+cardInfo.activeCardId)[0];
        let root = grid.getEl().dom;
        let rows = Ext.query('td.td-svgGrid', root);
        jsPlumbInstance[cardInfo.thisNumberInstance].makeTarget($(rows), {
          isTarget: true,
          isSource: false
        });
        console.log('svgContainer-'+cardInfo.activeCard);
      },
      failure: function(response, opts) {
        console.log(response);
      }
    });
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
          /* Ошибка при перерисовки после удаления слайда из середины */
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
        cls: 'svgDescription-'+cardInfo.activeCard,
        id: 'svgDescription-'+cardInfo.activeCard+'-'+cardInfo.countItemCard,
        x: 660, y: 270
      }
    );
    // jsPlumbInstance[cardInfo.thisNumberInstance].connect({
    //   source: 'svgDescription-'+cardInfo.activeCard+'-'+cardInfo.countItemCard,
    //   target: $('.svgContainer-'+cardInfo.activeCard),
    //   scope: 'someScope'
    // });
    jsPlumbInstance[cardInfo.thisNumberInstance].addEndpoint('svgDescription-'+cardInfo.activeCard+'-'+cardInfo.countItemCard, { uuids: 'svgDescription-'+cardInfo.activeCard+'-'+cardInfo.countItemCard, isSource: true });
    console.log('svgDescription-'+cardInfo.activeCard)
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
          /* Ошибка при перерисовки после удаления слайда из середины */
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
    let allInstanceConnections = jsPlumbInstance[cardInfo.thisNumberInstance].getConnections({
      scope: 'someScope'
    });
    if (allInstanceConnections.length > 0) {
      jsPlumbInstance[cardInfo.thisNumberInstance].deleteEveryEndpoint();
    }
    while (cardInfo.activeCardEl.items.items[0]) {
      cardInfo.activeCardEl.remove(cardInfo.activeCardEl.items.items[0]);
    }
  },
  onCheckInsctance: function() {
    let cardInfo = this.onGetBaseCard();
    let allInstanceConnections = jsPlumbInstance[cardInfo.thisNumberInstance].getConnections();
    console.log('uuids_ist::'+jsPlumbInstance[cardInfo.thisNumberInstance].Defaults.uuids);
    // console.log(jsPlumbInstance[cardInfo.thisNumberInstance]);
    console.log(allInstanceConnections);
  },
  onToPdf: function() {
    let cardInfo = this.onGetBaseCard();
    let svgDraw = document.querySelectorAll('#svgDraw-'+cardInfo.activeCard+' > svg');
    // let svgDesc = document.querySelectorAll('#svgDescription-1-1');
    // console.log(svgDesc[0].outerHTML)
    if(svgDraw.length > 0) {
      let doc = new window.PDFDocument({compress: false});
      let svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' + svgDraw[0].outerHTML + '</svg>';
      // console.log(svg);
      SVGtoPDF(doc, svg, 20, 20);
      // console.log(navigator);
      let stream = doc.pipe(blobStream());
      stream.on('finish', function() {
        let blob = stream.toBlob('application/pdf');
        var blobUrl = URL.createObjectURL(blob);
        window.open(blobUrl);
      });
      doc.end();
    } else {
      console.log('add svg draw to page (svgDraw.length <= 0)');
    }
  },
  onTcPdf: function() {
    Ext.create('Ext.window.Window', {
      autoShow: true,
      title: 'TCPdf',
      height: '90%',
      width: '90%',
      layout: 'fit',
      items: [{
        xtype: 'component',
        autoEl: {
            tag: 'iframe',
            width: '100%',
            height: '100%',
            src: 'app/phps/tcpdf.php',
            scrolling: 'yes'
        }
      }]
    })
  },
  onCanvasPdf: function() {
    html2canvas(document.body, {
      onrendered: function (canvas) {
          new Ext.Window({
              title: 'Screenshot',
              width: '95%',
              height: '95%',
              resizable: true,
              autoScroll: true,
              preventBodyReset: true,
              html: '<img src="' +canvas.toDataURL("image/png") +'" height="1000"/>'
          }).show();
      }
    });
  }
});
