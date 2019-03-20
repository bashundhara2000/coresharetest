command -v heroku

if [ $? -ne 0 ]
then
	echo "Heroku CLI not installed "
	exit 1

fi
if [ ! -f ~/.netrc ]; then
    echo "Heroku credentials not found! Please run heroku login and authenticate"
fi

heroku git:remote -a desolate-atoll-62947
heroku config
heroku run php /app/artisan migrate
heroku run php /app/artisan db:seed

git push origin master
git push heroku master
