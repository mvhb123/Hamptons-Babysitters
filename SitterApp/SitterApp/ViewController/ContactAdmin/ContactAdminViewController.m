//
//  ContactAdminViewController.m
//  SitterApp
//
//  Created by Vikram gour on 22/02/16.
//  Copyright © 2016 Sofmen. All rights reserved.
//

#import "ContactAdminViewController.h"

@interface ContactAdminViewController ()

@end

@implementation ContactAdminViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Contact Admin";
    NSURL *url = [NSURL URLWithString:kUrlForContactAdmin];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    [self.webViewForExp loadRequest:request];
    self.view.backgroundColor=kBackgroundColor;
    self.webViewForExp.backgroundColor=kBackgroundColor;
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
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[error localizedDescription] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert show];
}
/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

@end
