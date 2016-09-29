//
//  AppDelegate.h
//  ParentApp
//
//  Created by Vikram gour on 06/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SMCoreNetworkFramework.h"
@class HomePageViewController;
@class LoginViewController;
// Debug levels: off, error, warn, info, verbose
static const DDLogLevel ddLogLevel = DDLogLevelVerbose;
@interface AppDelegate : UIResponder <UIApplicationDelegate,SMCoreNetworkManagerDelegate>
@property (strong, nonatomic) UIWindow *window;
@property (strong, nonatomic) LoginViewController *loginView;
@property (strong, nonatomic) HomePageViewController *homePageView;
@property (nonatomic, retain) UINavigationController *navigationController;
@property (nonatomic, strong)NSString *str_jobId;
@property (nonatomic, strong)NSString *notificationId;
- (void)setApplicationTheme;
-(void)reSetNotificationCount;

@end

