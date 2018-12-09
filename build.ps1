Set-Alias sz "C:\Program Files\7-Zip\7z.exe"

$package = "at.megathorx.wsc_api.tar"
$files = ("package.xml", "aclOption.xml", "acpMenu.xml", "objectType.xml", 
          "objectTypeDefinition.xml", "option.xml", "userGroupOption.xml", 
          "userNotificationEvent.xml", "install.sql", "update_1.3.0.sql", "language")


if (!(Test-Path -Path build)) {
   New-Item -ItemType directory -Path build
}

if (!(Test-Path -Path build_tmp)) {
   New-Item -ItemType directory -Path build_tmp
}

if (Test-Path -Path files) {
    cd files
    sz a -ttar ../build_tmp/files.tar *
    cd ..
}

if (Test-Path -Path templates) {
    cd templates
    sz a -ttar ../build_tmp/templates.tar *
    cd ..
}

if (Test-Path -Path acptemplates) {
    cd acptemplates
    sz a -ttar ../build_tmp/acptemplates.tar *
    cd ..
}

foreach ($file in $files) {
    Copy-Item $file -Destination "build_tmp/." -Recurse
}

$filename = "../build/" + $package

cd build_tmp
sz a -ttar $filename *
cd ..

Remove-Item -Path build_tmp -Force -Recurse