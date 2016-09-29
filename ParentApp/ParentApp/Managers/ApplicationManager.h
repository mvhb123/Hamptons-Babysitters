//
//  ApplicationManager.h
//
//  Created by Vikram Gour on 27/10/14.
//  Copyright (c) 2014 Sofmen. All rights reserved.
//
#import <Foundation/Foundation.h>
#import <UIKit/UIKit.h>
#import "Parent.h"
#import "Children.h"
#import "JobList.h"
#import "Sitter.h"
#import "SMCoreNetworkManager.h"

@interface ApplicationManager : NSObject<SMCoreNetworkManagerDelegate> {
    Children *childrenInfo;
    JobList *jobInfo;
    NSMutableArray *array_statelist;
   
}
@property (assign,nonatomic) BOOL isNotification;
@property (strong, nonatomic) Parent *parentInfo;
@property (strong, nonatomic) JobList *jobDetail;
@property (strong, nonatomic) Sitter *sitterInfo;
@property (strong, nonatomic) NSMutableArray *array_stateList;
@property (strong, nonatomic) NSMutableArray *array_sitterRequirement;
@property (strong, nonatomic) NSMutableArray *array_selectedChild;
@property (strong, nonatomic) NSMutableArray *array_selectedChildren;
@property (strong, nonatomic) NSMutableArray *array_childRecord;
@property (strong, nonatomic) NSMutableArray *array_jobList;
@property (strong, nonatomic) NSMutableArray *array_jobChildRecord;
@property (strong, nonatomic) NSMutableArray *array_jobChildDetail;
@property (strong, nonatomic) NSString *str_startDate;


+(ApplicationManager *)getInstance;
- (void)showAlertForVC:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message;
- (BOOL)isDeviceProxyIsEnabled;
- (void)saveLogInData:(NSDictionary *) dict_data;
- (void)saveChildData:(NSMutableArray*) array_childData;
- (void)saveJobList:(NSMutableArray *) array_jobList;
- (void)saveJobDetail:(NSDictionary *) dict_jobDetail;
- (void)saveSitterDetail:(NSDictionary *)dict_sitterDetail;
- (void)updateBadgeCount:(NSString *)notificationId;
- (void)updateAppNotificationSeting:(NSString *)strNotify;
- (NSString*)setLocalTime:(id)vc withTime:(NSDate *)date andDateFormat:(NSDateFormatter *)dateFormatter;
+(UIImage *)imageWithColor:(UIColor *)color;
@end
