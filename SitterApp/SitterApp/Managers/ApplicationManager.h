//
//  ApplicationManager.h
//
//  Created by Vikram Gour on 27/10/14.
//  Copyright (c) 2014 Sofmen. All rights reserved.
//
#import <Foundation/Foundation.h>
#import <UIKit/UIKit.h>
#import "Sitter.h"
#import "JobList.h"
#import "SitterAdditionInformation.h"
#import "NumberFormatter.h"
#import "Children.h"
@interface ApplicationManager : NSObject<SMCoreNetworkManagerDelegate>
{
   
}
@property(strong,nonatomic) Sitter *sitterInfo;
@property(strong,nonatomic) JobList *jobList;
@property(strong,nonatomic) SitterAdditionInformation *sitterAdditionInfo;
@property(assign,nonatomic) BOOL isNotification;
@property (nonatomic ,strong)NumberFormatter *numFormatter;

+(ApplicationManager *)getInstance;
- (void)showAlertForVC:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message;
-(void)saveLogInData:(NSDictionary *)dict_data;
-(void)saveAdditionalInformation:(NSDictionary *)dict_data;
-(void)saveJobList:(NSDictionary *)dict_data andJobType:(NSString*)jobType;
- (void)logOutAPI:(NSMutableDictionary *)dict_logOut;
- (void)updateBadgeCount:(NSString *)notificationId;
-(NSString *)convertDateFormate:(NSString *)str_date andDateFormate:(NSString *)dateFormate;
- (void)updateAppNotificationSeting:(NSString *)strNotify;
-(UIView*)createViewForchildInfo:(NSDictionary*)dictChildInfo frame:(CGRect)frm;
-(UILabel*)getEmptyLabel;
+(UIImage *)imageWithColor:(UIColor *)color;
@end
