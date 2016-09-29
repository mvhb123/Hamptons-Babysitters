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


-(void) loginUser:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber;
-(void) forgotPassword:(NSMutableDictionary*)dict_forgotPasswordData forRequestNumber:(NSInteger )requestNumber;
-(void)getAllAdditionalInfo:(NSMutableDictionary*)dict_Data forRequestNumber:(NSInteger )requestNumber;
-(void) updateUserProfile:(NSMutableDictionary*)dict_userData imageData:(NSData*)imgData forRequestNumber:(NSInteger )requestNumber;
-(void)getJobList:(NSMutableDictionary *)dict_job forRequestNumber:(NSInteger)requestNumber;
-(void)getJobDetail:(NSMutableDictionary *)dict_job forRequestNumber:(NSInteger)requestNumber;
-(void)logOut:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)changePassword:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)updateAppNotificationSetting:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)cancelJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)acceptJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)completeJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)relationShipList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber;
-(void)specialNeedList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber;
-(void)AddChild:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber images:(NSData*)Image;
-(void)updateSitterStatus:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
-(void)reSetNotofication:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber;
@end
