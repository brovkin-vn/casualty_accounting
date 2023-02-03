<?php

require("config.php");

if (isset($_GET['ids']))  $ids = $_GET['ids'];else  $ids = '-1';
if (strlen($ids) == 0)   $ids  ='-1';
if (isset($_GET['dt1_check']))  $dt1_check  = $_GET['dt1_check'];  else $dt1_check = 0;
if (isset($_GET['dt1']))        $dt1        = $_GET['dt1'];        else $dt1 = 0;
// if (isset($_GET['mine_check'])) $mine_check = $_GET['mine_check']; else $mine_check = 0;
// if (isset($_GET['mine_id']))    $mine_id    = $_GET['mine_id'];    else $mine_id = 0;



$link = mysqli_connect($HostCA, $UserCA, $PassCA, $DBCA);

if (!$link) {
  echo '{"result": "Ошибка соединения c БД"}';
  exit(0);
}
mysqli_query($link, 'set charset utf8');

$query = "select ttt.risk, ttt.risk2, ttt.measure_text
, IF(ttt.last_dt = '2000-01-01', 'Н/Д', ttt.cnt) cnt
, IF(ttt.last_dt = '2000-01-01', 'Н/Д', ttt.last_dt) last_dt
, IF(ttt.last_dt = '2000-01-01', 'Н/Д', ttt.full_years) full_years
, ttt.degree_name, ttt.max_degree_value, ttt.score_char, ttt.score,
-- select ttt.risk, ttt.risk2, ttt.measure_text, ttt.cnt, ttt.last_dt, ttt.full_years, ttt.degree_name, ttt.max_degree_value, ttt.score_char, ttt.score,
CASE
  WHEN ttt.score IN ('1A', '1B', '2A') THEN 'green'
  WHEN ttt.score IN ('1C', '1D', '1E', '2B', '2D', '2C', '3A', '3B', '3C', '4A') THEN 'yellow'
  WHEN ttt.score IN ('2E', '3D', '3E', '4B', '4C') THEN 'orange'
  ELSE 'red'
END score_color,
CASE
  WHEN ttt.score IN ('2E', '3D', '3E', '4B', '4C', '4D', '4E', '5A', '5B', '5C', '5D', '5E') THEN 'Неприемлемый'
  ELSE 'Приемлемый'
END score_text
from
(
select tt.risk, tt.risk2, tt.measure_text, tt.cnt, tt.last_dt, tt.full_years, tt.degree_name, tt.max_degree_value
, 
CASE
  WHEN tt.full_years <= 1 THEN 'E'
  WHEN tt.full_years <= 3 THEN 'D'
  WHEN tt.full_years <= 5 THEN 'C'
  WHEN tt.full_years <= 10 THEN 'B'
  ELSE 'A'
END score_char,
CONCAT(tt.max_degree_value, 
  CASE
    WHEN tt.full_years <= 1 THEN 'E'
    WHEN tt.full_years <= 3 THEN 'D'
    WHEN tt.full_years <= 5 THEN 'C'
    WHEN tt.full_years <= 10 THEN 'B'
  ELSE 'A'
END
) score
from
(
select t.process_name risk, t.event_name risk2, t.measure_text,t.cnt, t.last_dt, 
(
  (YEAR(CURRENT_DATE) - YEAR(last_dt)) -                             /* step 1 */
  (DATE_FORMAT(CURRENT_DATE, '%m%d') < DATE_FORMAT(last_dt, '%m%d')) /* step 2 */
) AS full_years,
t.max_degree_value + t.group_flag as max_degree_value, dg2.degree_name
from
(
select /*prof_name,*/ process_name, event_name, event_name2, coalesce(m.measure_text,'') measure_text, (pass_time) min_pass_time, max(risk_degree_value) max_degree_value, max(group_flag) group_flag, date(max(dt)) last_dt, count(*) cnt  from ca
join measure m on (m.process_id = ca.process_id and m.event_id = ca.event_id)
join prof pr on pr.id = ca.prof_id
join event ev on ev.id = ca.event_id
join process ps on ps.id = ca.process_id
join degree dg on dg.id = ca.degree_id
where ca.deleted=0
and prof_id in (" . $ids . ")";
if ($dt1_check) $query = $query." and ca.dt > '".$dt1."'";
$query = $query." and ca.process_id > 0 
and ca.event_id > 0 
group by /*prof_id, prof_id,*/ ca.process_id, ca.event_id, measure_text
order by process_name, event_name
) t
JOIN degree dg2 on dg2.risk_degree_value = t.max_degree_value
) tt
) ttt";
// echo $query;
$rez = mysqli_query($link, $query);
$row_num = 0;
if ($rez) {
  while ($row = mysqli_fetch_assoc($rez)) {
    $card[] = $row;
    $row_num++;
  }
}
if ($row_num == 0) {
  while ($field = mysqli_fetch_field($rez)) {
    $fields[$field->name] = "-";
  }
  $card  = [$fields];
}

mysqli_close($link);

?>
