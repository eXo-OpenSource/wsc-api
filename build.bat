@echo off
cd build
del files.tar
del at.megathorx.wsc-api.tar

cd ../files
"C:\Program Files\7-Zip\7z.exe" a -ttar ../build/files.tar *
cd ../build
"C:\Program Files\7-Zip\7z.exe" a -ttar at.megathorx.wsc-api.tar files.tar ../package.xml ../option.xml ../language
del files.tar
cd ..