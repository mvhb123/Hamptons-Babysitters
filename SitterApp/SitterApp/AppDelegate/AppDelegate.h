//
//  AppDelegate.h
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <SMCoreComponent/SMCoreComponent.h>
#import "SMCoreNetworkFramework.h"
#import "LoginViewController.h"
#import "HomeViewController.h"
#import "AppBaseViewController.h"
// Debug levels: off, error, warn, info, verbose
static const DDLogLevel ddLogLevel = DDLogLevelVerbose;
@interface AppDelegate : UIResponder <UIApplicationDelegate,SMCoreNetworkManagerDelegate>
@property (strong, nonatomic) UIWindow *window;
@property (strong,nonatomic) UINavigationController *navigationController;
@property (strong,nonatomic)LoginViewController *viewLogin;
@property (strong,nonatomic)HomeViewController *viewHome;
@property (assign,nonatomic)int jobId;
@property (strong,nonatomic)NSString *notificationId;
@property (strong,nonatomic)NSString *job_status;
-(void)setRootViewAfterLogin;
-(void)setRootViewAfterLogout;
-(void)reSetNotificationCount;
@end

