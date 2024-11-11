Every 6 hours the project analyzes the IATA requests from last 24 hours. The result is pushed to "iata_request_history" table. If any airports have been requested fewer then 50 times in last 24 hours the warning email with the list of these airports is sent. 

Email settings in SendMail.php (specified as comments).

Database settings in models/Database.php

Cron Job to run the IATA analysis every 6 hours and to clear rows older then 1 year:
    Run crontab -e;
    Then:

    FOR ANALYSIS:
    0 */6 * * * /usr/bin/curl http://hostaddress/request_controller/index.php >> /var/log/your_app/cron_output.log 2>&1
    (Set 'hostaddress' and 'your_app' name)
    FOR CLEARING:
    0 0 1 * * /usr/bin/curl -X DELETE http://hostaddress/request_controller/index.php >> /var/log/your_app/cron_output.log 2>&1

    