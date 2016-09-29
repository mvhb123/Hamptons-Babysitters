//
//  SMCoreNetrateworkManager.h
//  SMCoreNetworkFramework
//
//  Created by Tushar on 22/01/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <SMCoreComponent/SMCoreComponent.h>
#import "AFHTTPSessionManager.h"
#import "AFNetworking.h"
@protocol SMCoreNetworkManagerDelegate <NSObject>

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                       withTask:(NSURLSessionDataTask *)responsedTask
                                     withRequestId:(NSUInteger)requestId;

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId;

@optional

- (void)requestSucceedWithResponseObject:(id)responseObject
                                withTask:(NSMutableDictionary *)responsedTask;
- (void)requestReceivedNoResponseWithSuccess:(id)responseObject;
- (void)requestFailWithErrorObject:(id)error;
- (void)requestSucceedWithImageData:(id)imageData ;
- (void)requestSucceedWithImageData:(id)imageData withRequestId:(NSUInteger)requestId;

@end


@interface SMCoreNetworkManager : NSObject{
    NSArray *array;
    AFHTTPSessionManager *_afSessionHTTPClient;
}

@property (nonatomic, weak) id <SMCoreNetworkManagerDelegate> delegate;
@property (nonatomic, strong) NSString *urlForNetworkRequest;
@property (nonatomic, strong) AFHTTPSessionManager *afSessionHTTPClient;

- (id)initWithBaseURLString:(NSString *)urlString;
- (void) setTimeoutInterval:(NSTimeInterval)interval;
- (void) cancelAllHTTPOperations;
- (void) cancelAllCurrentRequest;
- (void) deRegisterCoreNetworkDelegate;



- (void) loginUser:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber;
- (void) forgotPassword:(NSMutableDictionary*)dict_forgotPasswordData forRequestNumber:(NSInteger )requestNumber;
- (void) userRegistration:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber images:(NSArray*)arrayImages;
- (void) getStateList:(NSMutableDictionary*)dict_country_id forRequestNumber:(NSInteger )requestNumber;
- (void) childrenList:(NSMutableDictionary*)dict_ChildrenData forRequestNumber:(NSInteger )requestNumber;
- (void) AddChild:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber images:(NSData*)Image;
- (void) JobsType:(NSMutableDictionary*)dict_jobsData forRequestNumber:(NSInteger )requestNumber;
- (void) CancleJob:(NSMutableDictionary*)dict_cancelJobData forRequestNumber:(NSInteger )requestNumber;
- (void) requestSitter:(NSMutableDictionary*)dict_requestSitterData forRequestNumber:(NSInteger )requestNumber;
- (void) logOut:(NSMutableDictionary*)dict_LogOut forRequestNumber:(NSInteger)requestNumber;
- (void) packeageList:(NSMutableDictionary*)dict_packageData forRequestNumber:(NSInteger)requestNumber;
- (void) bookinFee:(NSMutableDictionary*)dict_addJobRequirement forRequestNumber:(NSInteger)requestNumber;
- (void) addJobRequest:(NSMutableDictionary*)dict_addJobData forRequestNumber:(NSInteger)requestNumber;
- (void) JobsDetail:(NSMutableDictionary*)dict_jobDetailData forRequestNumber:(NSInteger)requestNumber;
- (void) updateUserProfile:(NSMutableDictionary*)dict_updateProfileData forRequestNumber:(NSInteger )requestNumber;
- (void) changePassword:(NSMutableDictionary*)dict_changePwd forRequestNumber:(NSInteger)requestNumber;
- (void) sitterDetail:(NSMutableDictionary*)dict_sitterDetailData forRequestNumber:(NSInteger)requestNumber;
- (void) getSavedCard:(NSDictionary *)dict_cardData forRequestNumber:(NSInteger)requestNumber;
- (void) buyCredits:(NSDictionary *)dict_buyCredits forRequestNumber:(NSInteger)requestNumber;
- (void) addEditCardDetail:(NSDictionary *)dict_editCardInfo forRequestNumber:(NSInteger)requestNumber;
- (void) updateAppNotificationSetting:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)relationShipList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber;
-(void)specialNeedList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber;
-(void)reSetNotofication:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
@end
