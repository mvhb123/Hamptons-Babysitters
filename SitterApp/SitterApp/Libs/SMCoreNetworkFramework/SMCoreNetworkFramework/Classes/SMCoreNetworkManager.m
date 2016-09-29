//
//  SMCoreNetworkManager.m
//  SMCoreNetworkFramework
//
//  Created by Tushar on 22/01/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "SMCoreNetworkManager.h"


@interface SMCoreNetworkManager()
@property (nonatomic, assign) NSTimeInterval timeoutInterval;
@end

@implementation SMCoreNetworkManager
@synthesize delegate = _delegate;
@synthesize afSessionHTTPClient = _afSessionHTTPClient;


- (void)initializeClass{
    
}

- (id)init{
    self=[super init];
    if (!self) {
        return nil;
    }
    return self;
}

- (id)initWithBaseURLString:(NSString *)urlString{
    self = [super init];
    if (!self) {
        return nil;
    }
    self.afSessionHTTPClient = [[AFHTTPSessionManager alloc] initWithBaseURL:[NSURL URLWithString:urlString]];
    self.urlForNetworkRequest = urlString;
    return self;
}


- (void) setTimeoutInterval:(NSTimeInterval)interval{
    self.timeoutInterval = interval;
}

- (void) cancelAllHTTPOperations{
    
}

- (void) cancelAllCurrentRequest{
    
}

- (void) deRegisterCoreNetworkDelegate{
    self.delegate = nil;
}



#pragma mark - ProjectMethods

-(void) loginUser:(NSMutableDictionary*)dict_loginData forRequestNumber:(NSInteger )requestNumber{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_loginData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
    
}

-(void) forgotPassword:(NSMutableDictionary*)dict_forgotPasswordData forRequestNumber:(NSInteger )requestNumber{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_forgotPasswordData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}

-(void)getAllAdditionalInfo:(NSMutableDictionary*)dict_Data  forRequestNumber:(NSInteger )requestNumber{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
    
}

-(void) updateUserProfile:(NSMutableDictionary*)dict_userData imageData:(NSData*)imgData forRequestNumber:(NSInteger )requestNumber{

    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURL *URL = [NSURL URLWithString:self.urlForNetworkRequest];

    AFHTTPSessionManager *manager = [[AFHTTPSessionManager alloc] initWithBaseURL:URL sessionConfiguration:configuration];
    manager.responseSerializer = [AFJSONResponseSerializer serializer];
    manager.requestSerializer = [AFJSONRequestSerializer serializer];
    
    [manager POST:self.urlForNetworkRequest parameters:dict_userData constructingBodyWithBlock:^(id<AFMultipartFormData> formData) {
        if(imgData!=nil){
            [formData appendPartWithFileData:imgData name:@"profile_pic" fileName:[NSString stringWithFormat:@"%@.jpg",[dict_userData objectForKey:@"profileImageName"]] mimeType:@"image/jpeg"];
        }
        
    } success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}

-(void)getJobList:(NSMutableDictionary *)dict_job forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_job success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)getJobDetail:(NSMutableDictionary *)dict_job forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_job success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)logOut:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}

-(void)changePassword:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}

-(void)updateAppNotificationSetting:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)cancelJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)acceptJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)completeJob:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)relationShipList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        // DDLogInfo(@"%@",error);
    }];
    
}
-(void)specialNeedList:(NSMutableDictionary*)dict forRequestNumber:(NSInteger )requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        // DDLogInfo(@"%@",error);
    }];
    
}
-(void)AddChild:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber images:(NSData*)Image
{
    //DDLogInfo(@"final registration key is %@",dict_registrationData);
    NSURL *URL = [NSURL URLWithString:self.urlForNetworkRequest];
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    AFHTTPSessionManager *manager = [[AFHTTPSessionManager alloc] initWithBaseURL:URL sessionConfiguration:configuration];
    manager.responseSerializer = [AFJSONResponseSerializer serializer];
    manager.requestSerializer = [AFJSONRequestSerializer serializer];
    
    [manager POST:self.urlForNetworkRequest parameters:dict_registrationData constructingBodyWithBlock:^(id<AFMultipartFormData> formData) {
        if(Image!=nil){
            //[formData appendPartWithFileData:Image name:@"child_pic" fileName:[NSString stringWithFormat:@"profilepic.jpg"] mimeType:@"image/jpeg"];
            [formData appendPartWithFileData:Image name:@"child_pic" fileName:[NSString stringWithFormat:@"%@.jpg",[dict_registrationData objectForKey:@"child_pic"]] mimeType:@"image/jpeg"];
            
        }
        
    } success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)updateSitterStatus:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)reSetNotofication:(NSMutableDictionary *)dict_Data forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_Data success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        NSLog(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
@end
