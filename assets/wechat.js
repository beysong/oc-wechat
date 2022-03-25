(function ($) {
  $.ajax({
    url: "/beysong/wechat/jsconfig",
    data: { url: window.location.href },
  }).then((config) => {
    wx.config(config);
  });
  wx.ready(function () {
    $.request("onGetShareData").then((res) => {
      console.log("res", res);
      wx.updateAppMessageShareData({
        title: "", // 分享标题
        desc: "", // 分享描述
        link: window.location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "", // 分享图标
        ...res,
        success: function () {
          // 设置成功
        },
      });

      wx.updateTimelineShareData({
        title: "", // 分享标题
        link: window.location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "", // 分享图标
        ...res,
        success: function () {
          // 设置成功
        },
      });
    });
  });
  wx.error(function (err) {});
})(jQuery);
