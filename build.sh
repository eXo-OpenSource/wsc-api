#!/bin/sh
cd build
rm at.megathorx.api.tar
rm at.megathorx.wsc-api.tar

cd ../files
tar -cvf ../build/files.tar lib
cd ../templates
tar -cvf ../build/templates.tar *
cd ../build
cp ../package.xml package.xml
cp ../option.xml option.xml
cp -r ../language language
tar -cvf at.megathorx.wsc-api.tar package.xml files.tar templates.tar option.xml language

rm files.tar
rm templates.tar
rm package.xml
rm option.xml
rm -rf language
