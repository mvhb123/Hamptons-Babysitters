//
//  AppDelegate.m
//  ParentApp
//
//  Created by Vikram gour on 06/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppDelegate.h"
#import "LoginViewController.h"
#import "HomePageViewController.h"
#import "JobDetailViewController.h"
#import "ApplicationManager.h"
#import <Instabug/Instabug.h>
@interface AppDelegate ()

@end

@implementation AppDelegate

NSString * const StripePublishableKey = @"pk_live_sOQaWjjDn86i4sPAlYHx1zCC";
- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {
    // Override point for customization after application launch.
    // Configure CocoaLumberjack
    [DDLog addLogger:[DDASLLogger sharedInstance]];
    [DDLog addLogger:[DDTTYLogger sharedInstance]];
    
    //Configure Instabug
    [Instabug startWithToken:@"2b7f66a3f1ddc69ae9c154192b2ea2a5" captureSource:IBGCaptureSourceUIKit invocationEvent:IBGInvocationEventShake];
    
    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    
    self.loginView = [[LoginViewController alloc]
                      initWithNibName:@"LoginViewController" bundle:nil];
    
    self.navigationController = [[UINavigationController alloc]
                                 initWithRootViewController:self.loginView];
      self.window.rootViewController = self.navigationController;
    self.window.backgroundColor = [UIColor whiteColor];
    [self setApplicationTheme];
    [self.window makeKeyAndVisible];
    //Register for push notification
    if ([application respondsToSelector:@selector(registerUserNotificationSettings:)]) {
        UIUserNotificationType userNotificationTypes = (UIUserNotificationTypeAlert |
                                                        UIUserNotificationTypeBadge |
                                                        UIUserNotificationTypeSound);
        UIUserNotificationSettings *settings = [UIUserNotificationSettings settingsForTypes:userNotificationTypes
                                                                                 categories:nil];
        [application registerUserNotificationSettings:settings];
        // [application registerForRemoteNotifications];
    } else {
        // Register for Push Notifications before iOS 8
        [application registerForRemoteNotificationTypes:(UIRemoteNotificationTypeBadge |
                                                         UIRemoteNotificationTypeAlert |
                                                         UIRemoteNotificationTypeSound)];
    }
    
    NSString *bundleId = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleIdentifier"];
    KeychainItemWrapper *keychainItem = [[KeychainItemWrapper alloc] initWithIdentifier:bundleId accessGroup:nil];
    if([[keychainItem objectForKey:(__bridge id)(kSecAttrAccount)] isEqual:@""]){
        [keychainItem setObject:[self generateUUID] forKey:(__bridge id)(kSecAttrAccount)];
    }
   
       return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application {
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application {
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application {
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application {
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application {
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}
- (BOOL)application:(UIApplication *)application
            openURL:(NSURL *)url
  sourceApplication:(NSString *)sourceApplication
         annotation:(id)annotation {
    // attempt to extract a token from the url
    DDLogInfo(@"%@",url);
    return YES;
}
/**
 This method is used for register device for Notification
 
 */
#pragma mark - User define methods
- (void)setApplicationTheme
{
    [[UIApplication sharedApplication] setStatusBarStyle:UIStatusBarStyleLightContent];
    [[UINavigationBar appearance] setTintColor:[UIColor whiteColor]];
    [[UINavigationBar appearance] setBarTintColor:kNavigationBarColor];
    NSDictionary *textAttributes = [NSDictionary dictionaryWithObjectsAndKeys:
                                    [UIColor whiteColor] ,NSForegroundColorAttributeName,
                                    [UIFont fontWithName:RobotoRegularFont size:17],NSFontAttributeName,
                                    nil];
   
    
    [[[self navigationController] navigationBar] setTitleTextAttributes:textAttributes];
   
    
}
- (NSString *)generateUUID {
    CFUUIDRef theUUID = CFUUIDCreate(NULL);
    CFStringRef string = CFUUIDCreateString(NULL, theUUID);
    CFRelease(theUUID);
    return (__bridge  NSString *)string;
}
-(void)reSetNotificationCount{
    SMCoreNetworkManager *networkManager;
    NSString *string_Url=[NSString stringWithFormat:@"%@",kResetNotification_API];
    networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    networkManager.delegate = self;
    NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
    [dict_JobRequest setSafeValue:[ApplicationManager getInstance].parentInfo.parentUserId forKey:@"userid"];
    [dict_JobRequest setSafeValue:[ApplicationManager getInstance].parentInfo.tokenData forKey:@"token"];
    [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    
    [networkManager reSetNotofication:dict_JobRequest forRequestNumber:0];
}
#pragma mark Push Notification
- (void)application:(UIApplication *)application didRegisterUserNotificationSettings:(UIUserNotificationSettings *)notificationSettings
{
    //register to receive notifications
    [application registerForRemoteNotifications];
}
- (void)application:(UIApplication *)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData *) deviceTokenAPNS
{
    NSString *deviceToken = [[[[deviceTokenAPNS description] stringByReplacingOccurrencesOfString:@"<"withString:@""] stringByReplacingOccurrencesOfString:@">" withString:@""] stringByReplacingOccurrencesOfString: @" " withString: @""];
    [[NSUserDefaults standardUserDefaults] setObject:deviceToken forKey:DEVICE_TOKEN];
    [[NSUserDefaults standardUserDefaults] synchronize];
    // Reset the counter at launch time
    [UIApplication sharedApplication].applicationIconBadgeNumber = 0;
    DDLogInfo(@"Device Token- %@",deviceToken);
}
- (void)application:(UIApplication *)application didFailToRegisterForRemoteNotificationsWithError:(NSError *)error {
    DDLogInfo(@"Failed to register for push notifications: %@", error);
}
NSDictionary *apnsInfo;
#pragma mark ReceiveRemoteNotification delegate
-(void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo{
    
    apnsInfo=[userInfo mutableCopy];
    NSDictionary *dict_notification=[userInfo safeObjectForKey:kAPS];
    DDLogInfo(@"%@",apnsInfo);
    self.str_jobId = [NSString stringWithFormat:@"%@",[[[userInfo safeObjectForKey:@"aps"]safeObjectForKey:kData]safeObjectForKey:kJobId]];
    if (application.applicationState == UIApplicationStateActive)
    {
        // Nothing to do if applicationState is Inactive, the iOS already displayed an alert view.
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Alert"
                                                            message:[NSString stringWithFormat:@"%@", [[userInfo safeObjectForKey:@"aps"] safeObjectForKey:@"alert"]]
                                                           delegate:self
                                                  cancelButtonTitle:@"Cancel"
                                                  otherButtonTitles:@"View",nil];
        [alertView show];
        self.notificationId=[NSString stringWithFormat:@"%@",[[dict_notification safeObjectForKey:kData] safeObjectForKey:kNotificationId]];
        [ApplicationManager getInstance].isNotification=TRUE;
        [UIApplication sharedApplication].applicationIconBadgeNumber = [[[userInfo safeObjectForKey:@"aps"]safeObjectForKey:@"badge"] intValue];
    }
    else
    {
        [[ApplicationManager getInstance]updateBadgeCount:self.notificationId];
        if ([[[self.navigationController viewControllers] lastObject] isKindOfClass:[LoginViewController class]]){
            
        }
        else
        {
            [[ApplicationManager getInstance]updateBadgeCount:self.notificationId];
            HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
            DDLogInfo(@"id--%@",self.str_jobId);
            homeView.str_jobId = [self.str_jobId mutableCopy];
            [self.navigationController pushViewController:homeView animated:NO];
            
            
            }
        
    }
}
#pragma mark - SMCoreNetworkManagerDelegate
-(void)requestDidSucceedWithResponseObject:(id)responseObject
                                  withTask:(NSURLSessionDataTask *)task
                             withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    
    switch (requestId) {
        case 0:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj valueForKey:kData] safeObjectForKey:@"badge"] intValue];
            }
            else
            {
                
            }
            break;
            
            
        default:
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    
    //[[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
}

#pragma mark -- alert view Tag

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    
    DDLogInfo(@"button index=%@",[self.navigationController viewControllers]);
    if (buttonIndex == 1) {
        if ([[[self.navigationController viewControllers] lastObject] isKindOfClass:[LoginViewController class]]){
        }
        else
        {
            [[ApplicationManager getInstance]updateBadgeCount:self.notificationId];
            HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
            homeView.str_jobId = [self.str_jobId mutableCopy];
            [self.navigationController pushViewController:homeView animated:NO];
        }
        
    }
        
    
}
@end
