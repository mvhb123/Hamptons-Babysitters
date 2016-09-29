//
//  ContactUsViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ContactUsViewController.h"

@interface ContactUsViewController ()

@end

@implementation ContactUsViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Contact Us";
    NSURL *url = [NSURL URLWithString:kContactUsUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    [web_view loadRequest:request];
    self.view.backgroundColor=kViewBackGroundColor;


}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
#pragma mark- delegate methods of WebView
- (void)webViewDidStartLoad:(UIWebView *)webView{
    [self startNetworkActivity:YES];
}
- (void)webViewDidFinishLoad:(UIWebView *)webView{
    [self stopNetworkActivity];
}
- (void)webView:(UIWebView *)webView didFailLoadWithError:(NSError *)error{
    DDLogInfo(@"%@",error);
    [self stopNetworkActivity];
    [self showAlertForSelf:self withTitle:nil andMessage:[error localizedDescription]];
    
}
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert setTag:1001];
    [alert show];
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (alertView.tag == 1001) {
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
    
}

#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 6://for logout
            [self logout:dict_responseObj];
            break;
            
    }
}
@end
