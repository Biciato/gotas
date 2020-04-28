//Função de pipelines para a datatables, que funcionará com AJAX
$.fn.dataTable.pipeline = function ( opts ) {
     // Configuration options
     var conf = $.extend( {
         pages: 1,     // number of pages to cache
         url: null,      // script url
         data: null,   // function or object with parameters to send to the server
         method: 'POST' // Ajax HTTP method
     }, opts );
     // Private variables for storing the cache
     var cacheLower = -1;
     var cacheUpper = null;
     var cacheLastRequest = null;
     var cacheLastJson = null;
     return function ( request, drawCallback, settings ) {

         var ajax          = false;
         var requestStart  = request.start;
         var drawStart     = request.start;
         var requestLength = request.length;
         var requestEnd    = requestStart + requestLength;
         
         if ( settings.clearCache ) {
             // API requested that the cache be cleared
             ajax = true;
             settings.clearCache = false;
         }
         else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
             // outside cached data - need to make a request
             ajax = true;
         }
         else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                   JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                   JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
         ) {
             // properties changed (ordering, columns, searching)
             ajax = true;
         }
         // Store the request for checking next time around
         cacheLastRequest = $.extend( true, {}, request );
  
         if ( ajax ) {
             // Need data from the server
             if ( requestStart < cacheLower ) {
                 requestStart = requestStart - (requestLength*(conf.pages-1));
  
                 if ( requestStart < 0 ) {
                     requestStart = 0;
                 }
             }
              
             cacheLower = requestStart;
             cacheUpper = requestStart + (requestLength * conf.pages);
  
             request.start = requestStart;
             request.length = requestLength*conf.pages;
            
             // Provide the same `data` options as DataTables.
             if ( $.isFunction ( conf.data ) ) {
                 // As a function it is executed with the data object as an arg
                 // for manipulation. If an object is returned, it is used as the
                 // data object to submit
                 var d = conf.data( request );
                 if ( d ) {
                     $.extend( request, d );
                 }
             }
             else if ( $.isPlainObject( conf.data ) ) {
                 // As an object, the data given extends the default
                 $.extend( request, conf.data );
             }
             //precisamos colocar o seletor da tabela no request pra ir como dado e pá
             $.extend(request, {tb_selector: conf.tb_selector});
             var selector = conf.tb_selector;
             //Vamos passar tbm o custom_data
             $.extend(request, {custom_data: conf.custom_data});

             settings.jqXHR = $.ajax( {
                 "type":     conf.method,
                 "url":      conf.url,
                 "data":     request,
                 "cache":    false,
                 "dataType": 'html text json',
                 "beforeSend": function()
                 {
                   console.log(request);
                   console.log(conf);
                 },
                 "success":  function ( json ) {
                   json = json.data_table_source;
                   $.each(json.data, function(i, item)
                     {
                       json.data[i] = conf.rowModifier(item);
                     });
                   console.log(json);
                     cacheLastJson = $.extend(true, {}, json);
  
                     if ( cacheLower != drawStart ) {
                         json.data.splice( 0, drawStart-cacheLower );
                     }
                     if ( requestLength >= -1 ) {
                         json.data.splice( requestLength, json.data.length );
                     }
                      
                     drawCallback( json );

                    $(document).trigger('dt_is_loaded');
                    $(selector).trigger('dt_is_loaded');
                    //Guarda os dados que vieram do back-end numa var
                    window[selector + "_table_data"] = json;
                 }
                  
             } );
         }
         else {
             json = $.extend( true, {}, cacheLastJson );
             json.draw = request.draw; // Update the echo for each response
             json.data.splice( 0, requestStart-cacheLower );
             json.data.splice( requestLength, json.data.length );
    
             drawCallback(json);
         }
        
     }
 };
  
 // Register an API method that will empty the pipelined data, forcing an Ajax
 // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
 $.fn.dataTable.Api.register( 'clearPipeline()', function () {
     $(this).trigger('pipeline_cleared');
     return this.iterator( 'table', function ( settings ) {
         settings.clearCache = true;
     } );
 } );

var initPipelinedDT = function(tb_selector, columns, ajax_url, order, custom_data, lengthMenu, drawCallback, rowModifier)
  {
    if(typeof order === 'undefined')
      {
        order = [[ 0, "asc" ]];
      }
    if(typeof custom_data === 'undefined')
      {
        custom_data = function(d)
        {
            return d;
        };
      }
    if(typeof lengthMenu === 'undefined')
      {
        lengthMenu = [ 5, 15, 20, 100 ];
      }
    if(typeof drawCallback === 'undefined')
      {
        drawCallback = function()
          {}
      }
    if(typeof rowModifier === 'undefined')
      {
        rowModifier = function(row_data)
          {
            return row_data;
          }
      }
    window[tb_selector] = $(tb_selector).DataTable( {
      columns: columns,
      searchDelay: 400,
      order: order,
      //responsive: true,
      lengthMenu: lengthMenu,
      dom: 'lfrtip',
      scrollX: '110%',
      language: {url: '/lang.json'},
      processing: true,
      serverSide: true,
      bDeferRender: true,
      bAutoWidth: true,
      ajax: $.fn.dataTable.pipeline( {
         url: ajax_url,
         pages: 1, // number of pages to cache,
         tb_selector: tb_selector, //Seletor da table que pode ser usado no back end
         data: custom_data,
         rowModifier: rowModifier
      } ),
      drawCallback: drawCallback
    } );
  }