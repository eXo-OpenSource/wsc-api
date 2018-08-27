#!/bin/sh
cd build
rm at.megathorx.api.tar
rm at.megathorx.wsc-api.tar

cd ../files
tar -cvf ../build/files.tar lib
cd ../templates
tar -cvf ../build/templates.tar *
cd ../acptemplates
tar -cvf ../build/acptemplates.tar *
cd ../build
cp ../package.xml package.xml
cp ../option.xml option.xml
cp ../install.sql install.sql
cp ../acpMenu.xml acpMenu.xml
cp ../aclOption.xml aclOption.xml
cp ../objectType.xml objectType.xml
cp ../objectTypeDefinition.xml objectTypeDefinition.xml
cp ../userGroupOption.xml userGroupOption.xml
cp -r ../language language
tar -cvf at.megathorx.wsc-api.tar package.xml files.tar templates.tar acptemplates.tar acpMenu.xml userGroupOption.xml aclOption.xml objectType.xml objectTypeDefinition.xml option.xml install.sql language

rm files.tar
rm templates.tar
rm acptemplates.tar
rm acpMenu.xml
rm package.xml
rm option.xml
rm objectType.xml
rm objectTypeDefinition.xml
rm aclOption.xml
rm userGroupOption.xml
rm install.sql
rm -rf language
