## 针对zimuzu.tv的小爬虫

封装了几个方法，可模拟登陆，抓取剧集信息，抓取剧集图片，抓取下载链接等

### 用法

__获取剧集放送状态暂未完成__

        $spider = new Spider('摩登家庭');
        $spider->login('小白机', 'WZRJJ888');

获取一季的下载链接

        $season = $spider->get_season(2);

获取第二季第一集的电驴链接

        $episode = $spider->get_episode(2, 1, 'ed2k', 'MP4');

获取基本信息

        $baseinfo = $spider->get_baseinfo();

获取主演

        $starring = $spider->get_starring();

