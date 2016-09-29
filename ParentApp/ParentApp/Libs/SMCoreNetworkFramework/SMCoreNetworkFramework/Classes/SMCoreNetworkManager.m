//
//  SMCoreNetworkManager.m
//  SMCoreNetworkFramework
//
//  Created by Tushar on 22/01/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "SMCoreNetworkManager.h"
//#import "AppBaseViewController.h"


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

- (void) loginUser:(NSMutableDictionary*)dict_loginData forRequestNumber:(NSInteger )requestNumber{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSString *deviceToken = [userDefaults objectForKey:@"deviceToken"];
    [dict_loginData setSafeObject:deviceToken forKey:@"deviceToken"];
   // DDLogInfo(@"%@",dict_loginData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_loginData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
       // DDLogInfo(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
    
}

-(void) forgotPassword:(NSMutableDictionary*)dict_forgotPasswordData forRequestNumber:(NSInteger )requestNumber{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_forgotPasswordData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        //DDLogInfo(@"%@",error);
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}

-(void)userRegistration:(NSMutableDictionary*)dict_registrationData forRequestNumber:(NSInteger )requestNumber images:(NSArray*)arrayImages {
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSString *deviceToken = [userDefaults objectForKey:@"deviceToken"];
   // DDLogInfo(@"final registration key is %@",dict_registrationData);
    [dict_registrationData setSafeObject:deviceToken forKey:@"deviceToken"];
    NSURL *URL = [NSURL URLWithString:self.urlForNetworkRequest];
    NSURLSessionConfiguration *configuration = [NSURLSessionConfiguration defaultSessionConfiguration];
    AFHTTPSessionManager *manager = [[AFHTTPSessionManager alloc] initWithBaseURL:URL sessionConfiguration:configuration];
    manager.responseSerializer = [AFJSONResponseSerializer serializer];
    manager.requestSerializer = [AFJSONRequestSerializer serializer];
    
    [manager POST:self.urlForNetworkRequest parameters:dict_registrationData constructingBodyWithBlock:^(id<AFMultipartFormData> formData) {
        if([arrayImages count]>0){
            for (int j=0;j<[arrayImages count]; j++){
                
                NSDictionary *dict_image=[arrayImages objectAtIndex:j];
                [formData appendPartWithFileData:[dict_image objectForKey:@"pic_data"] name:[dict_image objectForKey:@"pictureName"] fileName:[NSString stringWithFormat:@"%@.jpg",[dict_image objectForKey:@"pictureName"]] mimeType:@"image/jpeg"];
              
            }
        }
        
    } success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
    }];
}
-(void)getStateList:(NSMutableDictionary*)dict_country_id forRequestNumber:(NSInteger )requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_country_id success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        
        //DDLogInfo(@"%@",error);
    }];

}

-(void)childrenList:(NSMutableDictionary*)dict_ChildrenData forRequestNumber:(NSInteger )requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_ChildrenData success:^(NSURLSessionDataTask *task, id responseObject) {
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
-(void)updateUserProfile:(NSMutableDictionary*)dict_updateProfileData forRequestNumber:(NSInteger )requestNumber
{
   // DDLogInfo(@"dict update profile key %@",dict_updateProfileData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_updateProfileData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];

}

- (void) JobsType:(NSMutableDictionary*)dict_jobsData forRequestNumber:(NSInteger )requestNumber
{
    //DDLogInfo(@"dict is %@",dict_jobsData);
   // NSString *url_String = [NSString stringWithFormat:@"%@/%d",self.urlForNetworkRequest,pageNo];
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_jobsData success:^(NSURLSessionDataTask *task, id responseObject){
        
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        //DDLogInfo(@"%@",error);
    }];

}

-(void)CancleJob:(NSMutableDictionary*)dict_cancelJobData forRequestNumber:(NSInteger )requestNumber
{
    //DDLogInfo(@"dict is %@",dict_cancelJobData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_cancelJobData success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];
 
}
-(void)requestSitter:(NSMutableDictionary*)dict_requestSitterData forRequestNumber:(NSInteger )requestNumber
{
   // DDLogInfo(@"dict is %@",dict_requestSitterData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_requestSitterData success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        //DDLogInfo(@"%@",error);
    }];

}
-(void)logOut:(NSMutableDictionary*)dict_LogOut forRequestNumber:(NSInteger)requestNumber
{
   // DDLogInfo(@"%@",dict_LogOut);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_LogOut success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];
}

-(void)packeageList:(NSMutableDictionary*)dict_packageData forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"%@",dict_packageData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_packageData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];

}
-(void)bookinFee:(NSMutableDictionary*)dict_addJobRequirement forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"%@",dict_addJobRequirement);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_addJobRequirement success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];

}
-(void)addJobRequest:(NSMutableDictionary*)dict_addJobData forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"%@",dict_addJobData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_addJobData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];

}
-(void)JobsDetail:(NSMutableDictionary*)dict_jobDetailData forRequestNumber:(NSInteger)requestNumber{
   // DDLogInfo(@"%@",dict_jobDetailData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_jobDetailData success:^(NSURLSessionDataTask *task, id responseObject) {
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
        
    } failure:^(NSURLSessionDataTask *task, NSError *error) {
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
        //DDLogInfo(@"%@",error);
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
-(void)changePassword:(NSMutableDictionary*)dict_changePwd forRequestNumber:(NSInteger)requestNumber
{
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_changePwd success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];
}
-(void)sitterDetail:(NSMutableDictionary*)dict_sitterDetailData forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"dict is %@",dict_sitterDetailData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_sitterDetailData success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];
  
}
- (void) getSavedCard:(NSDictionary *)dict_cardData forRequestNumber:(NSInteger)requestNumber
{
   // DDLogInfo(@"dict is %@",dict_cardData);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_cardData success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
      //  DDLogInfo(@"%@",error);
    }];

}
- (void) buyCredits:(NSDictionary *)dict_buyCredits forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"dict is %@",dict_buyCredits);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_buyCredits success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
    }];

}
- (void) addEditCardDetail:(NSDictionary *)dict_editCardInfo forRequestNumber:(NSInteger)requestNumber
{
    //DDLogInfo(@"dict is %@",dict_editCardInfo);
    [self.afSessionHTTPClient POST:self.urlForNetworkRequest parameters:dict_editCardInfo success:^(NSURLSessionDataTask *task, id responseObject){
        [self.delegate requestDidSucceedWithResponseObject:responseObject withTask:task withRequestId:requestNumber];
    } failure:^(NSURLSessionDataTask *task, NSError *error){
        [self.delegate requestDidFailWithErrorObject:error withRequestId:requestNumber];
       // DDLogInfo(@"%@",error);
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
