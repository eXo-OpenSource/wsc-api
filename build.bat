@echo off
cd build
del files.tar
del at.megathorx.wsc-api.tar

cd ../files
"C:\Program Files\7-Zip\7z.exe" a -ttar ../build/files.tar *
cd ../acptemplates
"C:\Program Files\7-Zip\7z.exe" a -ttar ../build/acptemplates.tar *
cd ../build
"C:\Program Files\7-Zip\7z.exe" a -ttar at.megathorx.wsc-api.tar files.tar acptemplates.tar ../package.xml ../option.xml ../acpMenu.xml ../install.sql ../objectType.xml ../objectTypeDefinition.xml ../aclOption.xml ../language
del files.tar
del acptemplates.tar
cd ..