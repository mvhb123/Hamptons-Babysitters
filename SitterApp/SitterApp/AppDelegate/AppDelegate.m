//
//  AppDelegate.m
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppDelegate.h"
#import <Instabug/Instabug.h>
#import "JobDetailViewController.h"
#import "ActiveJobDetailViewController.h"
@interface AppDelegate ()

@end

@implementation AppDelegate 
@synthesize viewHome;
@synthesize viewLogin;

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {
    // Override point for customization after application launch.
    // Configure CocoaLumberjack
    [DDLog addLogger:[DDASLLogger sharedInstance]];
    [DDLog addLogger:[DDTTYLogger sharedInstance]];
    
    //Configure Instabug
    [Instabug startWithToken:@"943bba4dfe10653c52752e0767dbe809" captureSource:IBGCaptureSourceUIKit invocationEvent:IBGInvocationEventShake];
    
    //Configure crashlytics
    //[Crashlytics startWithAPIKey:@"474ea690458c51b8a5458c4b89d28d0e9c72c333"];
    
    
    
    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    [self setApplicationTheme];
    NSMutableDictionary *dict_logedInUserDetail=[[[NSUserDefaults standardUserDefaults] objectForKey:kLogedinUserDetail] mutableCopy];
    if (dict_logedInUserDetail!=nil) {
        [[ApplicationManager getInstance]saveLogInData:dict_logedInUserDetail];
        [self setRootViewAfterLogin];
        [self reSetNotificationCount];
    }else{
      [self setRootViewAfterLogout];
    }
    
    [self.window makeKeyAndVisible];
    //[application registerForRemoteNotifications];
    if ([application respondsToSelector:@selector(registerUserNotificationSettings:)]) {
        UIUserNotificationType userNotificationTypes = (UIUserNotificationTypeAlert |
                                                        UIUserNotificationTypeBadge |
                                                        UIUserNotificationTypeSound);
        UIUserNotificationSettings *settings = [UIUserNotificationSettings settingsForTypes:userNotificationTypes
                                                                                 categories:nil];
        [application registerUserNotificationSettings:settings];
        
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

#pragma mark - User define methods
- (NSString *)generateUUID {
    CFUUIDRef theUUID = CFUUIDCreate(NULL);
    CFStringRef string = CFUUIDCreateString(NULL, theUUID);
    CFRelease(theUUID);
    return (__bridge  NSString *)string;
}
-(void)setRootViewAfterLogin{
    self.viewHome=[[HomeViewController alloc]initWithNibName:@"HomeViewController" bundle:nil];
    self.navigationController=[[UINavigationController alloc]initWithRootViewController:self.viewHome];
    [self.window setRootViewController:self.navigationController];
}

-(void)setRootViewAfterLogout{
    [[NSUserDefaults standardUserDefaults]removeObjectForKey:kLogedinUserDetail];
    [[NSUserDefaults standardUserDefaults]synchronize];
    self.viewLogin=[[LoginViewController alloc]initWithNibName:@"LoginViewController" bundle:nil];
    self.navigationController=[[UINavigationController alloc]initWithRootViewController:self.viewLogin];
    [self.window setRootViewController:self.navigationController];
    
}
- (void)setApplicationTheme
{
    [[UIApplication sharedApplication] setStatusBarStyle:UIStatusBarStyleLightContent];
    [[UINavigationBar appearance] setTintColor:[UIColor whiteColor]];
    [[UINavigationBar appearance] setBarTintColor:kNavigationBarColor];
    NSDictionary *textAttributes = [NSDictionary dictionaryWithObjectsAndKeys:
                                    [UIColor whiteColor] ,NSForegroundColorAttributeName,
                                    [UIFont fontWithName:Roboto_Regular size:17],NSFontAttributeName,
                                    nil];
    
    [[[self navigationController] navigationBar] setTitleTextAttributes:textAttributes];
   
    
}
-(void)reSetNotificationCount{
    SMCoreNetworkManager *networkManager;
    NSString *string_Url=[NSString stringWithFormat:@"%@",kResetNotification_API];
    networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    networkManager.delegate = self;
    NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
    [dict_JobRequest setSafeValue:[ApplicationManager getInstance].sitterInfo.sitterId forKey:@"userid"];
    [dict_JobRequest setSafeValue:[ApplicationManager getInstance].sitterInfo.str_TokenData forKey:@"token"];
    [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];

    [networkManager reSetNotofication:dict_JobRequest forRequestNumber:0];
}
#pragma mark Push Notification
- (void)application:(UIApplication *)application didRegisterUserNotificationSettings:(UIUserNotificationSettings *)notificationSettings
{
    //register to receive notifications
    [application registerForRemoteNotifications];
    
}
- (void)application:(UIApplication *)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)deviceToken
{
    NSString *deviceTokens = [[[[deviceToken description] stringByReplacingOccurrencesOfString:@"<"withString:@""] stringByReplacingOccurrencesOfString:@">" withString:@""] stringByReplacingOccurrencesOfString: @" " withString: @""];
    // Store the token
    [[NSUserDefaults standardUserDefaults] setObject:deviceTokens forKey:DEVICE_TOKEN];
    [[NSUserDefaults standardUserDefaults] synchronize];
    DDLogInfo(@"Registered for push notifications and stored device token: %@", [[NSUserDefaults standardUserDefaults] objectForKey:DEVICE_TOKEN]);
}
- (void)application:(UIApplication *)application didFailToRegisterForRemoteNotificationsWithError:(NSError *)error {
    DDLogInfo(@"Failed to register for push notifications: %@", error);
    
}
- (void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo
{
   
    NSDictionary *dict_notification=[userInfo safeObjectForKey:kAPS];
    NSString *strLoginuseId=[ApplicationManager getInstance].sitterInfo.sitterId;
    NSString *strNotifyUser=[NSString stringWithFormat:@"%@",[[dict_notification safeObjectForKey:kData] safeObjectForKey:@"user_id"]];
    //UIApplicationState state = [application applicationState];
    if ([strLoginuseId isEqualToString:strNotifyUser])
    {
        if ([[[dict_notification safeObjectForKey:kData] safeObjectForKey:kJobId] isEqualToString:@""]) {
            Sitter *sitter=[ApplicationManager getInstance].sitterInfo;
            DDLogInfo(@"Status %@",[[dict_notification safeObjectForKey:kData] safeObjectForKey:kStatus]);
            sitter.sitterStatus=[[dict_notification safeObjectForKey:kData] safeObjectForKey:kStatus];
            [ApplicationManager getInstance].sitterInfo=sitter;
            [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationSitterStatus object:nil];
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_notification safeObjectForKey:kNotoficationAlert] delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
            [alert show];
        }else if([[[dict_notification safeObjectForKey:kData] safeObjectForKey:@"notification_type"] intValue] ==2){
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_notification safeObjectForKey:kNotoficationAlert] delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
            [alert show];
        }
        else if([[[dict_notification safeObjectForKey:kData] safeObjectForKey:@"notification_type"] intValue] ==3||[[[dict_notification safeObjectForKey:kData] safeObjectForKey:@"notification_type"] intValue] ==4){
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_notification safeObjectForKey:kNotoficationAlert] delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"View", nil];
            [alert setTag:101];
            [alert show];
            self.jobId=[[[dict_notification safeObjectForKey:kData] safeObjectForKey:kJobId] intValue];
            self.job_status=[[dict_notification safeObjectForKey:kData] safeObjectForKey:kJob_status];
            self.notificationId=[[dict_notification safeObjectForKey:kData] safeObjectForKey:kNotificationId];
            [ApplicationManager getInstance].isNotification=TRUE;
        }else{
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_notification safeObjectForKey:kNotoficationAlert] delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"View", nil];
            [alert setTag:100];
            [alert show];
            self.jobId=[[[dict_notification safeObjectForKey:kData] safeObjectForKey:kJobId] intValue];
            self.notificationId=[[dict_notification safeObjectForKey:kData] safeObjectForKey:kNotificationId];
            [ApplicationManager getInstance].isNotification=TRUE;
        }
    }
    [UIApplication sharedApplication].applicationIconBadgeNumber=[[dict_notification safeObjectForKey:kBadgeCount] intValue];
    DDLogInfo(@"RECEIVED MESSAGE: %@", userInfo);
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (buttonIndex==1 && alertView.tag==100) {// For new job notification
        if ([[[self.navigationController viewControllers] lastObject] isKindOfClass:[JobDetailViewController class]]){
            if ([[[self.navigationController viewControllers] lastObject] respondsToSelector:@selector(getJobDetail:)]) {
                JobDetailViewController *viewJObDetail=[[JobDetailViewController alloc]initWithNibName:@"JobDetailViewController" bundle:nil];
                viewJObDetail.jobId=self.jobId;
                [self.navigationController popToRootViewControllerAnimated:NO];
                [self.navigationController pushViewController:viewJObDetail animated:NO];
            }
        }
        else
        {
            JobDetailViewController *viewJObDetail=[[JobDetailViewController alloc]initWithNibName:@"JobDetailViewController" bundle:nil];
            viewJObDetail.jobId=self.jobId;
            [self.navigationController pushViewController:viewJObDetail animated:YES];
            [[ApplicationManager getInstance]updateBadgeCount:self.notificationId];
            
        }
    }if (buttonIndex==1 && alertView.tag==101) {// For new job notification
        if ([[[self.navigationController viewControllers] lastObject] isKindOfClass:[ActiveJobDetailViewController class]]){
            if ([[[self.navigationController viewControllers] lastObject] respondsToSelector:@selector(getJobDetail:)]) {
                if ([[self.job_status lowercaseString] isEqualToString:@"inactive"]) {
                    ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController_inactive" bundle:nil];
                   viewActiveJob.jobId=self.jobId;
                    viewActiveJob.jobTypeFlag=2;
                    [self.navigationController popToRootViewControllerAnimated:NO];
                    [self.navigationController pushViewController:viewActiveJob animated:NO];
                }else{
                    ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController" bundle:nil];
                   
                    viewActiveJob.jobId=self.jobId;
                    [self.navigationController popToRootViewControllerAnimated:NO];
                    [self.navigationController pushViewController:viewActiveJob animated:NO];
                }
            }
        }
        else
        {
            if ([[self.job_status lowercaseString] isEqualToString:@"inactive"]) {
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController_inactive" bundle:nil];
                
                viewActiveJob.jobId=self.jobId;
                viewActiveJob.jobTypeFlag=2;
                [self.navigationController popToRootViewControllerAnimated:NO];
                [self.navigationController pushViewController:viewActiveJob animated:NO];
            }else{
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController" bundle:nil];
                viewActiveJob.jobId=self.jobId;
                [self.navigationController pushViewController:viewActiveJob animated:NO];
            }

            
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
#pragma mark - Application state
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

@end
