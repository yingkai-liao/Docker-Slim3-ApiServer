#windows�Ҧ� ���h��windows docker-toolbox
https://www.docker.com/products/docker-toolbox
�M��b�ୱ�W�Ұ�"Docker Quickstart Terminal" 

1.�bwindows�Ҧ��U�A�n���N�M�ץؿ��Pdocker-machine�ؿ�mount�b�@�_
cd �M�ץؿ�(D:/YUIApp/server/)
sh windows.sh

2-1.��������A�Q��docker-compose����build+run
docker-compose up -d
docker-compose down

2-2��ʥ�build�Xdock�M���ɡA�A�ϥάM���ɲ���docker�e��
sh docker-build.sh
sh docker-run.sh
sh docker-stop.sh


3.���}browser ��J192.168.99.100�N�|�ݨ�e��
�o�O�]docker��vm���w�]IP�A�S��L���ܳ��O�o�ӼƦr

�p�G�Q�n�M����IP�s�� �o�����O�|��127.0.0.1��80port���W�h(�p�G�n����L�H�s�A�n��J10.0�}�Y����)
netsh interface portproxy add v4tov4 listenaddress=127.0.0.1 listenport=80 connectaddress=192.168.99.100 connectport=80