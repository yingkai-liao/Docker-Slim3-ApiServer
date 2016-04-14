#windows模式 先去裝windows docker-toolbox
https://www.docker.com/products/docker-toolbox
然後在桌面上啟動"Docker Quickstart Terminal" 

1.在windows模式下，要先將專案目錄與docker-machine目錄mount在一起
cd 專案目錄(D:/YUIApp/server/)
sh windows.sh

2-1.直接執行，利用docker-compose直接build+run
docker-compose up -d
docker-compose down

2-2手動先build出dock映像檔，再使用映像檔產生docker容器
sh docker-build.sh
sh docker-run.sh
sh docker-stop.sh


3.打開browser 輸入192.168.99.100就會看到畫面
這是跑docker的vm的預設IP，沒改過的話都是這個數字

如果想要和本機IP連接 這條指令會把127.0.0.1的80port接上去(如果要給其他人連，要輸入10.0開頭那個)
netsh interface portproxy add v4tov4 listenaddress=127.0.0.1 listenport=80 connectaddress=192.168.99.100 connectport=80