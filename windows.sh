#建立專案mount路徑(windows才需要)
PROJ_PATH=$PWD
docker-machine stop default
cd /c/Program\ Files/Oracle/VirtualBox
./VBoxManage sharedfolder add default --name project --hostpath $PROJ_PATH
docker-machine start default
docker-machine.exe ssh default 'sudo mkdir -p /project'
docker-machine.exe ssh default 'sudo mount -t vboxsf project /project'