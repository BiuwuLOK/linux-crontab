This is for `PHP` part descriptions.

```
# * * * * * stands for: Minutes, Hours, Days, Months, WeekDays.
```

![cronjob-syntax](https://raw.githubusercontent.com/senkoraku552/linux-crontab-staffs/main/images/cronjob_syntax.webp?raw=true)

Usage with linux crontab

```
# DEFINE TIMESTAMP
CURR_TIME = date +%Y-%m-%d,%H:%M:%S

# DEFINE LOG FILE DIR
LOG_DIR = /pathto/cron_logs/
# ---- ----

# 1/Hour single Cronjob
01 * * * * { $CURR*TIME; php /path/projectname/cronjob.php; } > ${LOG_DIR}/cronjob_log.txt 2>&1

# 1/Hour multiple Cronjob
01 * * * * { $CURR_TIME; php /path/projectname/cronjob_1.php; } > ${LOG_DIR}/cronjob_log_1.txt 2>&1
01 * * * * { $CURR_TIME; php /path/projectname/cronjob_2.php; } > ${LOG_DIR}/cronjob_log_2.txt 2>&1

# 1/Daily single Cronjob
02 04 * * * { $CURR_TIME; php /path/projectname/cronjob.php; } > ${LOG_DIR}/cronjob_log.txt 2>&1

# 1/Daily multiple Cronjob

03 04 * * _ { $CURR_TIME; php /path/projectname/cronjob_1.php; } > ${LOG_DIR}/cronjob_log_1.txt 2>&1
03 04 _ * * { $CURR_TIME; php /path/projectname/cronjob_2.php; } > ${LOG_DIR}/cronjob_log_2.txt 2>&1

## 1/Daily send email after all log ready
10 04 * * * { $CURR_TIME; php /path/LogFileReader.php; } > ${LOG_DIR}/final_cronjob_log.txt
```
