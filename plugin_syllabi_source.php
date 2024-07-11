<?php
function term_season($term) {
	$result = null;
	switch($term%10) {
		case 0: 
			$result = false;
			break;
		case 1:
			$result = 'fall';
			break;
		case 2:
			$result = 'spring';
			break;
		default:
			$result = 'summer';
			break;
	}
	return $result;
}
function term_year($term) {
	return floor($term/100);
}


	$dept_param = ($_GET && array_key_exists('dept', $_GET)) ? $_GET['dept'] : '';
	$dept_param = trim(strtoupper($dept_param));
	if (strlen($dept_param) <= 0 || $dept_param == 'A-Z') $dept_param = '';
	
	$no_instructor_display = 'STAFF';
	
	$cos_courses_sql_server = 'musql19-lstr.marshall.edu';
	$cos_courses_sql_info = array('Database'=>'COS_CourseInfo', 'UID'=>'COS_CourseInfo_Read', 'PWD'=>'zD2-y!!aJTpsBk?Tb4hY45%5');
	$cos_courses_conn = sqlsrv_connect($cos_courses_sql_server, $cos_courses_sql_info);
	
	$result = array();
	
	// get current year and season for display
	$cur_terms = array();
	$arc_terms = array();
	
	$sql = 'SELECT TOP 40 Semester, Code FROM BertSemesters WHERE isActive=1 ORDER BY Code DESC';
	$rs = sqlsrv_query($cos_courses_conn, $sql);
			
	if ($row = sqlsrv_fetch_array($rs)) {		
		// first result will be most recent/current
		$cur_season = term_season($row['Code']);
		$cur_year = term_year($row['Code']);			
		do {
			$year = term_year($row['Code']);
			if (term_season($row['Code']) == $cur_season && $year == $cur_year) {
				$cur_terms[] = $row['Code'];
				$arc_terms[] = $row['Code'];
			}
			else if ($year > date('Y') - 5) {
				$arc_terms[] = $row['Code'];
			}
			else {
				break;
			}
		} while ($row = sqlsrv_fetch_array($rs)); 
	}
	
	$sql_params = $dept_param ? $arc_terms : $cur_terms;
	$sql = 'SELECT * FROM vwDTSyllabi WHERE semesterCode IN (' . implode(',', array_fill(0, count($dept_param ? $arc_terms : $cur_terms), '?')) . ')';
	if ($dept_param) {
		$sql .= ' AND deptAbbreviation = ?';
		$sql_params[] = $dept_param;
	}
	$sql .= ' ORDER BY semesterCode DESC, subject, course, section';
	
	$rs = sqlsrv_query($cos_courses_conn, $sql, $sql_params);
	
	while ($row = sqlsrv_fetch_array($rs)) {
		$section = array(
			'id' 				=> $row['BertSyllabusID'],
			'semester' 			=> $row['Semester'], 
			'semesterCode' 		=> $row['SemesterCode'], 
			'department' 		=> $row['Department'], 
			'subject' 			=> $row['Subject'], 
			'course' 			=> $row['Course'],
			'section' 			=> $row['Section'],
			'title' 			=> $row['Title'], 
			'hours' 			=> $row['Hours'], 
			'syllabus' 			=> $row['Syllabus'], 
			'instructor' 		=> $row['Instructor'],
			'email' 			=> $row['Email'],
			'cssClass' 			=> (array_search($row['semesterCode'], $cur_terms) !== false ? 'cos_courses_current' : 'cos_courses_archive')
		);
		$result[] = $section;
	} 
 
	echo(json_encode($result));
	
	sqlsrv_free_stmt($rs);
	sqlsrv_close($cos_courses_conn);

?>
