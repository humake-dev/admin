<!DOCTYPE html>
<html>
<head>
  <title>테스트</title>
</head>
<body>
  <form action="https://192.168.0.26/entrances/add" method="post">
    <input type="hidden" name="format" value="html" />
    <input type="hidden" name="branch_id" value="36" />
    <input type="hidden" name="date" value="20180316" />
    <input type="hidden" name="time" value="144005" />
    <input type="text" name="card_no" />
    <input type="submit" value="제출" />
  </form>
</body>
</html>

-- To allow advanced options to be changed.
EXEC sp_configure 'show advanced options', 1;
GO
-- To update the currently configured value for advanced options.
RECONFIGURE;
GO
-- To enable the feature.
EXEC sp_configure 'xp_cmdshell', 1;
GO
-- To update the currently configured value for this feature.
RECONFIGURE;
GO


curl.exe -i -X POST -d "branch_id=36&card_no='+@cardID+'&date=&time=&format=json" https://dev.myfiterp.com/entrances/add
curl -i -X POST -d "branch_id=36&card_no=97981&date=20190410&time=101020&format=json" https://dev.myfiterp.com/entrances/add


INSERT INTO EventLogs(EventID,MachineTime,EventData,EventAddress,HostTime,SerialNo,Update_Y)
VALUES(130,'2018-03-30 19:01:03.000','d51972',0,'2018-03-30 19:01:03.000',146,'Y')
INSERT INTO Log_Device_Normal(LogDate,LogTime,LogType,LogStatus,LogPersonID) VALUES('20180316','145803','0','0',3629815252)





INSERT INTO user_fcs(user_id,fc_id,created_at,updated_at) SELECT u.id,1439,now(),now() FROM users as u LEFT JOIN user_fcs as uf ON u.id=uf.user_id WHERE u.branch_id=10 AND  uf.user_id is null 
SELECT a.id FROM (SELECT u.id FROM users as u INNER JOIN orders as o ON o.user_id=u.id INNER JOIN enrolls as e ON e.order_id=o.id WHERE u.branch_id=10 AND e.end_date<=curdate() GROUP BY u.id) as a




DELETE FROM humake_development.messages where enable=0