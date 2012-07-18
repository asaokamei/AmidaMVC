#AmidaMVCフレームワーク ![profile](<?php echo $_ctrl->getBaseUrl( '/common/img/big-tec.jpg'); ?>)

一体いくつあるんだ、状態ですが、
PHPのウェブアプリケーションフレームワーク（WAF）として
AmidaMVCを開発しています。

[githubで公開](https://github.com/asaokamei/AmidaMVC) しています。

まだまだ開発中で、ほぼアルファ状態です。

##フレームワークかCMSか

フレームワークとして開発を進めたのですが、比較的簡単に機能が追加できたので
気がついたらCMSのようになっていました。

マークダウン記法とHTMLに対応したpukiWikiと思ってください。

###CMSとしての特徴

特徴は、

*   国際化対応（言語および複数サイトを構築可能）
*   簡易ダイナミックメニュー実装
*   ファイルベース（DBは非必須）
*   マークダウン、テキストファイル、PHPソースコードをHTMLに自動変換
*   管理者機能でファイルの編集、追加、公開、がウェブから可能
*   twitterのbootstrapを利用したので最初からResponsiveデザイン対応

###フレームワークとしての特徴

*   Chain of Responsibility パターンを採用したコントローラー。
*   MVCは・・・対応中。

