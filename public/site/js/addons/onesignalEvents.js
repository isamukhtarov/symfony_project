window.OneSignal = window.OneSignal || [];
OneSignal.push(function() {
    /* FOR DEVELOPMENT */
    // OneSignal.init({
    //     appId: "b6edeaf1-85c7-4d9e-8f0c-01b48a7b45a0",
    //     notifyButton: {
    //         enable: true, // for production comment this
    //     },
    //     subdomainName: "reportloc",
    // });

    /* FOR PRODUCTION */
    OneSignal.init({
        appId: "2402213a-3a1f-42b0-bbdf-336cd1ee84d4",
    });
});