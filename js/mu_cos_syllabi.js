var $syllabi_tables = null;
jQuery.fn.dataTable.ext.search.push(
	function(settings, data, dataIndex) {
		var courses_current = jQuery('#cos_courses_current').prop('checked');
		var collection = data[5] || 'cos_courses_archive';
		if (courses_current && collection == 'cos_courses_archive') {
			return false;
		}
		return true;
	}
);
jQuery(function() {
	$syllabi_tables = jQuery('table.cos_syllabi').DataTable( {
		'ajax': {
			'url': 'https://netapps.marshall.edu/cosweb/www/syllabi/plugin_syllabi_source.php' + (typeof(mu_cos_syllabiDept) !== 'undefined' ? '?dept=' + mu_cos_syllabiDept : ''),
			'dataSrc': ''
		},
		'columnDefs': [ 
			{ 'targets': 0,  'data': { _: 'semester', 'sort': 'semesterCode' } }, 
			{ 'targets': 1,  'data': function(row) {
				row.sectionIdentifier = row.subject + ' ' + row.course + ' : ' + row.section;
				if (row.syllabus && row.syllabus.length > 0) {
					return '<a class="icon syllabus" target="_blank" href="' + row.syllabus + '" title="Syllabus for ' + row.sectionIdentifier + '">' + row.sectionIdentifier + '</a>';
				}
				else {
					return row.sectionIdentifier;
				}
			} }, 
			{ 'targets': 2, 'data': 'title' },
			{ 'targets': 3,  'data': 'hours' }, 
			{ 'targets': 4,  'data': function(row) {
				var ins = '';
				if (!row.instructor || row.instructor.length < 1 || row.instructor == 'STAFF') {
					ins = 'STAFF';
				}
				else {
					var ins_a = row.instructor.split('*');
					var eml_a = row.email.split('*');
					for (i=0; i<ins_a.length; i++) {
						ins += '<a class="icon email" href="mailto:' + eml_a[i] + '" title="Email instructor for ' + row.sectionIdentifier + '">' + ins_a[i] + '</a> ';
					}
				}
				return ins;
			} }, 
			{ 'targets': 5,  'data': 'cssClass', 'visible': false },
		],
		'deferRender': true, 
		'dom': '<"top">rt<"bottom"ilp><"clear">',
		'language': {
			'loadingRecords': 	'Loading - please wait...',
			'emptyTable': 		'There are no courses to display',
			'zeroRecords': 		'There are no courses that match the filters',
			'info': 			'Showing _START_ to _END_ of _TOTAL_ courses',
			'infoEmpty': 		'Showing 0 courses',
			'infoFiltered': 	'(filtered from _MAX_)',
			'paginate': 		{ 'previous': 'Prev' }
		},
		'pageLength': 20,
		'lengthMenu': [[20, 50, -1], [20, 50, "All"]],
		'order': [[0,'desc'],[1,'asc']]
	} );
	
	$container = jQuery('<div class="cos_courses_filters" />');
	$search = jQuery('<span><label for="cos_courses_filter">Search: </label><input type="text" id="cos_courses_filter" name="cos_courses_filter" /></span>');
	$search.keyup(mu_cos_syllabi_updateFilters);
	if (typeof(mu_cos_syllabiDept) !== 'undefined') {
	    $radio_current = jQuery('<span><input type="radio" checked="checked" id="cos_courses_current" name="cos_courses_terms" /><label class="cos_courses" for="cos_courses_current"> Current Courses Only</label></span>');
	    $radio_archive = jQuery('<span><input type="radio" id="cos_courses_archive" name="cos_courses_terms" /><label class="cos_courses" for="cos_courses_archive"> Course Archive</label></span>');
	    $search.keyup(mu_cos_syllabi_updateFilters);
	    $radio_current.click(mu_cos_syllabi_updateFilters);
	    $radio_archive.click(mu_cos_syllabi_updateFilters);
	}
	$container.append($search);
	if (typeof(mu_cos_syllabiDept) !== 'undefined') { 
		$container.append($radio_current, $radio_archive);
	}
	jQuery('div.cos_syllabi').first().before($container);
});
function mu_cos_syllabi_updateFilters() {		
	var courses_param = jQuery('#cos_courses_filter').val();
	if (courses_param.length > 1) {
		$syllabi_tables.search(courses_param).draw();
	}
	else if (courses_param.length == 0) {
		$syllabi_tables.search('').draw();
	}
	
	$syllabi_tables.draw();
}
