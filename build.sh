#!/bin/bash
PACKAGE=at.megathorx.wsc_api.tar
FILES=(package.xml aclOption.xml acpMenu.xml objectType.xml objectTypeDefinition.xml option.xml userGroupOption.xml userNotificationEvent.xml install.sql update_1.3.0.sql language)

cleanup() {
    cd build
    FILES_COUNT=$(ls -l | grep -v ^l | wc -l)
    if [ $FILES_COUNT -ne 0 ]; then
        rm -r *
    fi
    cd ..
}

archive() {
    cd $2
    FILES_COUNT=$(ls -l | grep -v ^l | wc -l)
    if [ $FILES_COUNT -eq 0 ]; then
        tar -cf "../$1" -T /dev/null
    else
        tar -cf "../$1" *
    fi
    cd ..
}

copy_pips() {
    for i in "${FILES[@]}"
    do
        cp -r $i build_tmp/.
    done
}


if [ ! -d build ]; then
    mkdir build
fi

if [ ! -d build_tmp ]; then
    mkdir build_tmp
fi

cleanup

if [ -d files ]; then
    archive "build_tmp/files.tar" "files"
fi

if [ -d templates ]; then
    archive "build_tmp/templates.tar" "templates"
fi

if [ -d acptemplates ]; then
    archive "build_tmp/acptemplates.tar" "acptemplates"
fi

copy_pips

archive "build/$PACKAGE" "build_tmp"

rm -rf build_tmp