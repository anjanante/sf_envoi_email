//== Class definition

var DatatableHtmlTableDemo = function() {
  //== Private functions

  // demo initializer
  var demo = function() {

    var datatable = $('.m-datatable').mDatatable({
      data: {
        saveState: {cookie: false},
      },
      search: {
        input: $('#generalSearch'),
      },
      columns: [
        {
          field: 'Deposit Paid',
          type: 'number',
        },
        {
          field: 'Order Date',
          type: 'date',
          format: 'YYYY-MM-DD',
        },
      ],
      translate: {
        records: {
          processing: 'Veuillez patienter...',
          noRecords: 'Auncune donnée trouvée',
        },
        toolbar: {
          pagination: {
            items: {
              default: {
                first: 'Première',
                prev: 'Précédent',
                next: 'Suivant',
                last: 'Dernière',
              },
              info: 'Affiché {{start}} - {{end}} de {{total}} données',
            },
          },
        },
      },
    });
  };

  return {
    //== Public functions
    init: function() {
      // init dmeo
      demo();
    },
  };
}();

jQuery(document).ready(function() {
  DatatableHtmlTableDemo.init();
});