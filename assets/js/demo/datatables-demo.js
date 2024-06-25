// Call the dataTables jQuery plugin
$(document).ready(function() {
  var table=$('#dataTable').DataTable({
      dom: 'rtip',
            language: {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            }
    });
  });
  