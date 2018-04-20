export UPLOAD_STR=$(curl --request POST --header "PRIVATE-TOKEN: $API_TOKEN" --form "file=@build/at.megathorx.wsc-api.tar" https://git.heisi.at/api/v3/projects/$CI_PROJECT_ID/uploads)
export UPLOAD_FILE=$(echo $UPLOAD_STR | grep -oP '"markdown":"([^"]{1,})"' | sed 's/"markdown":"//g' | sed 's/"//g')
export UPLOAD_DATA="description=$UPLOAD_FILE"
echo $(curl --request POST --header "PRIVATE-TOKEN: $API_TOKEN" -d $UPLOAD_DATA https://git.heisi.at/api/v3/projects/$CI_PROJECT_ID/repository/tags/$CI_COMMIT_REF_NAME/release)