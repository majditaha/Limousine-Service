
expass project

Needs to manually add Laravel cron job on server:
################
run `crontab -e` and add line with correct path to project:
```
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

