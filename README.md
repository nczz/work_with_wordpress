# Getting Started

使用WordPress這套框架開發網站，以後端的角色跟前端夥伴合作，下方是一些經驗整理，常用外掛系列，裡面包含了很多組合，並非每個案子都會使用這些工具，根據情況調整與搭配自己或夥伴延伸開發的方式也是不少，但沒有這些工具，開發想必更為困難，感謝社群～

## 主要項目


### Post Type （新型態）類

WordPress 的核心架構，從這「型態」出發，延伸出各種內容來從前端顯示。

1. [Custom Post Type UI](https://tw.wordpress.org/plugins/custom-post-type-ui/) 建立新型態的文章內容，用來客製化不同內建文章/頁面的「標題」、「內文」結構形式。
2. [Advanced Custom Fields](https://tw.wordpress.org/plugins/advanced-custom-fields/) 當完成安裝與建立客製化新型態後，會發現那還只是跟內建的文章/頁面結構一樣，只是在後台選單上多了一個新的選項，這時候要搭配這款客製化定義欄位的工具，指定新型態格式，自定義該新型態內容使用哪些欄位。

### Capabilities （權限）類

扯到內容，就會有操作權限的管理，WordPress 在這部分並沒有像其他 CMS 有非常「計較」權限的管理，屬於大方向的命名權限，並針對該命名權限從程式中鎖定，不能說有強硬的限制開發或是使用某種框架（framework），這點既是彈性也是在開發給客戶時要注意的地方。

1. [User Role Editor](https://tw.wordpress.org/plugins/user-role-editor/) 從最基礎的權限（Capabilities）出發，可以配置給單獨的使用者或是角色，介面稍微陽春。
2. [Members](https://tw.wordpress.org/plugins/members/) 以角色（Role）出發，設定與編輯角色權限，把功能分類好的編輯界面滿舒適的。
3. [Groups](https://tw.wordpress.org/plugins/groups/) 群組的概念分配權限，提供群組的模式管理註冊者

三種權限管理類的外掛都有特色，以個人經驗區分使用情境為：客戶用戶數少且變化多適合第一種、用戶數多且單純適合二、三種。

### 類權限管理 類

從開發角度來去限制、管理其他自由開發的外掛本來就會造成架構難度提升，所以這個分類的概念就是「疊加」，透過別的外掛來更進階處理其他外掛的不足，真要說為什麼要這麼做，就是為了日後升級的彈性囉！

1. [Admin Menu Editor](https://tw.wordpress.org/plugins/admin-menu-editor/) 有時候因為要調整出客戶可讀性與方便性高的選單時就會需要他，擁有直接修改選單顯示名稱與排序功能
2. [Adminimize](https://tw.wordpress.org/plugins/adminimize/) WP 內建的權限設定沒有針對角色還有更細的增刪修查限制功能，同個權限下的使用者如果有希望不同的操作介面，就會需要使用這外掛去「隱藏」，做一些髒髒der事。

> 至於更細部的權限微調大部分都是直接coding在子主題下了！


### 頁面客製化 類

設計頁面（page）會碰到個問題：求速度與精緻常使用一些主題（theme）作為前導，但大部分主題強化前端視覺使用短碼（short code）的方式，其實不適用一般使用者（end-user），常有非單純重複貼文功能的頁面要讓客戶修改的時候，避免客戶誤觸雷區與使用者體驗提升，針對輸入的內容會需要程式化挖空，這點大部分就要透過下面的外掛來搭配了

1. [Pagely MultiEdit](https://tw.wordpress.org/plugins/pagely-multiedit/) 當頁面中有多個欄位是希望提供給客戶使用時，這套外掛可以做挖空，提供填空的方式修正頁面，其他資源可以參考[官方](https://pagely.com/multiedit-plugin/)教學
2. [Custom Post Widget](https://tw.wordpress.org/plugins/custom-post-widget/) 同樣屬於挖空部分內容提供客戶有固定區域修改文案的功能，結合小工具（widgets）或是自行開發延伸的方式都很方便
3. [Custom Login](https://tw.wordpress.org/plugins/custom-login/) 登入後台的樣式客製化，強化客戶歸屬感強化ＸＤ
4. [Customize Login Image](https://tw.wordpress.org/plugins/customize-login-image/) 同上，歸屬感強化的換登入介面工具


## 常見項目

下面的項目比較偏向綜合來看，大多客戶或開發上會需要的輔助

### 聯絡表單 類

1. [Contact Form 7](https://tw.wordpress.org/plugins/contact-form-7/) 說到表單一定要用這款
2. [Contact Form DB](https://tw.wordpress.org/plugins/contact-form-7-to-database-extension/) 送出去的表單通常是直接寄信到客戶那邊，用這款外掛可以做資料管理

### 活動事件 類

1. [Event Organiser](https://tw.wordpress.org/plugins/event-organiser/) 客戶公開活動、行程等這類需求也不少，需要的包涵時間地點等資訊也滿完整的
2. [Another Events Calendar](https://tw.wordpress.org/plugins/another-events-calendar/) 最近發現的外掛，跟上面有的功能差不多

### 發信與訂閱 類

1. [Postman SMTP Mailer/Email Log](https://tw.wordpress.org/plugins/postman-smtp/) 根據客戶需求，架構簡單的可以使用 SMTP 方式寄信，GMail 每日 250 封的限制其實滿夠大部分形象網站，這款支援使用Gmail API為比較正確的請求方式，建議！
2. [Easy WP SMTP](https://tw.wordpress.org/plugins/easy-wp-smtp/) 同上差不多類型
3. [WP Mail SMTP](https://tw.wordpress.org/plugins/wp-mail-smtp/) 同上差不多類型
4. [WP SES](https://tw.wordpress.org/plugins/wp-ses/) 有需要大量發送或是希望更穩定服務的客戶可以推薦使用 Amazon SES 服務
5. [Mailgun for WordPress](https://tw.wordpress.org/plugins/mailgun/) 或是使用 MailGun 的介接也滿方便與穩定
6. [Email Subscribers & Newsletters](https://tw.wordpress.org/plugins/email-subscribers/) 免費強大的訂閱系統，適合搜集名單，至於要發有追蹤系統流程類的EDM，還是建議用付費的專業系統，其他資訊可以參考[這邊](http://www.wpbeginner.com/wp-tutorials/how-to-add-email-subscriptions-for-your-wordpress-blog/)
7. [MailPoet Newsletters](https://tw.wordpress.org/plugins/wysija-newsletters/) 有搭配 autoresponder 的服務，客戶如有需要整合在網站的專業EDM服務可以使用這款


### WooCommerce 類

1. [WooCommerce Social Login - WordPress plugin](https://codecanyon.net/item/woocommerce-social-login-wordpress-plugin/8495883) 測試過免費的外掛，想改登入成功後的導向不行要硬改、然後混合式登入模式邏輯也有點怪，這款付費一次搞定
2. [WooCommerce Dynamic Pricing & Discounts](https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279) 目前套用各種客戶要求的折扣模式都還挺行的外掛
3. [WooCommerce Extended Coupon Features](https://tw.wordpress.org/plugins/woocommerce-auto-added-coupons/) 免費但也夠強的折扣外掛，組合上也很厲害
4. [WooCommerce Checkout Field Editor Pro](https://tw.wordpress.org/plugins/woo-checkout-field-editor-pro/) 結帳時的欄位調整
5. [WooCommerce Checkout Manager](https://tw.wordpress.org/plugins/woocommerce-checkout-manager/) 同上，調整結帳欄位時的方便工具
6. [Custom Related Products for WooCommerce](https://wordpress.org/plugins/custom-related-products-for-woocommerce/) woocommerce預設相關商品會抓取同分類下的所有商品，這外掛可以讓你自訂在前台呈現不同分類的相關商品

> WooCommerce光是折扣部份就有很多內容可以寫不完了，但應該不會有一款外掛打趴全部的情況（因為這可能會有很複雜難用的後作用）


### 效能調教 類

1. [W3 Total Cache](https://tw.wordpress.org/plugins/w3-total-cache/) 搭配這款外掛也建議從伺服器上跟進，安裝[一些快取機制的應用](https://easyengine.io/tutorials/php/memcache/)來強化，速度可以大幅提升
2. [Redis Object Cache](https://tw.wordpress.org/plugins/redis-cache/) Redis 做快取有連續性特性，性能也不錯，可以對付高流量站
3. [Incapsula](https://www.incapsula.com/) 免費CDN的選擇，這款有個Aggressive模式夠強
4. [Cloudflare](https://www.cloudflare.com/) 一般般的免費CDN

### 其他強化 類

1. [TinyMCE Advanced](https://tw.wordpress.org/plugins/tinymce-advanced/) 加強所見即所得編輯介面
2. [Easing Slider](https://tw.wordpress.org/plugins/easing-slider/) 有些客戶對於輪播圖希望有其他選項時可以使用這款，在開發上的組合也滿棒的
3. [Add Admin JavaScript](https://tw.wordpress.org/plugins/add-admin-javascript/) 以類權限部分，第一層客製化使用第一、二款外掛沒問題，如要針對功能內頁內的項目修正，除了直接 Hard Code 就是使用 JavaScript 來修正顯示
4. [Add Admin CSS](https://tw.wordpress.org/plugins/add-admin-css/) 同上的處理外掛
5. [Breadcrumb NavXT](https://tw.wordpress.org/plugins/breadcrumb-navxt/) 很強大的麵包屑管理工具，通常用來藏一些不該出現的集合頁連結

### SEO 類

1. [Yoast SEO](https://tw.wordpress.org/plugins/wordpress-seo/) 功能強大、適合進階
2. [All in One SEO Pack](https://tw.wordpress.org/plugins/all-in-one-seo-pack/) 功能算強、簡單適合客戶

### 進階 類

1. [Shortcodes Ultimate](https://tw.wordpress.org/plugins/shortcodes-ultimate/) 不同於購買的主題樣板，這外掛提供一些開源實用的短碼（shortcode）可以為進階使用者強化多樣性
2. [WPML](https://wpml.org/) 多國語言的強大外掛，但網站效能會因為他的各種強大功能給導致速度變慢，需搭配伺服器部分評估
3. [Safe Redirect Manager](https://tw.wordpress.org/plugins/safe-redirect-manager/) 雖然轉址這件事我都建議從伺服器上下手，但如果碰到無權限的狀況下，這工具就有不少幫助了
4. [Transients Manager](https://tw.wordpress.org/plugins/transients-manager/) 管理背景執行程序的好幫手，在網站覺得慢的時候使用它來清理一下
5. [Heartbeat Control](https://tw.wordpress.org/plugins/heartbeat-control/) 同上為優化外掛，管理 WordPress AJAX 發生的行為，過多的 AJAX 發生會導致網站執行效率變得很差，相關資訊可以[點此](https://woorkup.com/diagnose-admin-ajax-php-causing-slow-load-times-wordpress/)參考
6. [Core Control](https://tw.wordpress.org/plugins/core-control/) 管理 WordPress 核心 HTTP 模組 與其他功能的好工具
7. [Disable All WordPress Updates](https://tw.wordpress.org/plugins/disable-wordpress-updates/) 當客戶屬於非使用手動安裝的WordPress架構，通常會有個隱藏危險：`自動更新`，開發的網站如有版本相容問題就必須使用這工具，改為手動更新
8. [Table of Contents Plus](https://tw.wordpress.org/plugins/table-of-contents-plus/) 寫文章自動根據`<h1~h6>`來分段落做章節索引，很棒的免費工具
9. [Query Monitor](https://tw.wordpress.org/plugins/query-monitor/) 觀察WordPress活動與資料庫行為的除錯優化工具
10. [Akeeba Backup for WordPress](https://www.akeebabackup.com/products/akeeba-backup-wordpress.html) 完成開發後的備份轉移有他簡單不少


### 其他 類

其實到這個段落，也就是剩下一些比較特殊案例，通常這些都會用另一種外掛的方式：子主題內的 [functions.php](./functions.php) 來控制，不過根據使用的主題與常用的組合都太特殊，這邊僅列出每次都會使用的部分！

<script src="https://gist.github.com/nczz/8903b7a9db63af61a70ca260f56aa836.js"></script>

再來還有 `wp-config.php` 內會改到的一些控制項

`define('WP_MEMORY_LIMIT', '256M');`

> 提升WordPress能夠使用的記憶體資源（常會是購物車或是內建縮圖機制吃資源的主題），可以參考[這裡](https://docs.woocommerce.com/document/increasing-the-wordpress-memory-limit/)

`define('FS_METHOD', 'direct');` 

> 修正安裝外掛權限問題，如無法修正資料目錄權限時才使用，但這問題還是從伺服器端下手還是比較好

`define('WP_POST_REVISIONS', 3);`

> 限制3個文章版本存取，若改成 `false` 則是取消版本管理功能