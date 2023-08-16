jQuery(document).ready(function(){
    jQuery(document).on('click', '#export_report', function(){
        var pdfname = jQuery(this).attr('data-pdfname');
        var pdfdoc = new jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: 'a3',
            compress: true,
            fontSize: 8,
            lineHeight: 1,
            autoSize: false,
            printHeaders: true
        });
        pdfdoc.fromHTML(jQuery('.wrap-content').html(), 10, 10, {'width': 110});
        pdfdoc.save(pdfname+'.pdf');
    });
});