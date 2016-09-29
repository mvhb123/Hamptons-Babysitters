//
//  OurExpectationsViewController.m
//  SitterApp
//
//  Created by Vikram gour on 22/02/16.
//  Copyright © 2016 Sofmen. All rights reserved.
//

#import "OurExpectationsViewController.h"

@interface OurExpectationsViewController ()

@end

@implementation OurExpectationsViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Our Expectations";
    NSURL *url = [NSURL URLWithString:kUrlForOurExpectation];
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

@end
