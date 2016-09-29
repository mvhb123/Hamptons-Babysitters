//
//  RegistrationViewController.m
//  SitterApp
//
//  Created by Vikram gour on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "RegistrationViewController.h"

@interface RegistrationViewController ()

@end

@implementation RegistrationViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    NSURLRequest *requestObj = [NSURLRequest requestWithURL:[NSURL URLWithString:kSitterRegistrationUrl]];
    [self.webViewRegistration loadRequest:requestObj];
    [self.view setBackgroundColor:kBackgroundColor];
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController.navigationBar setHidden:NO];
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
#pragma mark - UIWebView delegates
- (void)webViewDidStartLoad:(UIWebView *)webView{
    //[self startNetworkActivity:NO];

}
- (void)webViewDidFinishLoad:(UIWebView *)webView{
    //[self stopNetworkActivity];

}
- (void)webView:(UIWebView *)webView didFailLoadWithError:(NSError *)error{
   // [self stopNetworkActivity];

    UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"error" message:[[error userInfo ] objectForKey:@"NSLocalizedDescription"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alertView show];
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
