//
//  ApplicationManager.m
//
//  Created by Vikram Gour on 27/10/14.
//  Copyright (c) 2014 Sofmen. All rights reserved.
//

#import "ApplicationManager.h"
@implementation ApplicationManager

+(ApplicationManager *)getInstance{
    static dispatch_once_t once;
    static ApplicationManager *sharedManager;
    dispatch_once(&once, ^ { sharedManager = [[self alloc] init]; });
    return sharedManager;
}

-(id)init{
    self = [super init];
    if (self) {
        //Allocate all variables
        array_statelist = [[NSMutableArray alloc]init];
        self.array_childRecord = [[NSMutableArray alloc]init];
        [self callStateListAPI];
    }
    return self;
}
# pragma mark- Method for showing generic alert
- (void)showAlertForVC:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
  
         //Show Alert with title and message only
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
    
}
- (BOOL)isDeviceProxyIsEnabled{
    CFDictionaryRef dicRef = CFNetworkCopySystemProxySettings();
    CFNumberRef number = CFDictionaryGetValue(dicRef,(const void *)kCFNetworkProxiesHTTPEnable);
    return (number!=0);//if number==0 then proxy is enabled and if non zero value is there then proxy is disabled
}
//- (void)updateAppNotificationSeting:(NSString *)strNotify{
//    NSMutableDictionary *dict_appSetting=[[NSMutableDictionary alloc] init];
//    [dict_appSetting setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
//    [dict_appSetting setSafeObject:self.parentInfo.tokenData forKey:kToken];
//    [dict_appSetting setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
//    [dict_appSetting setSafeObject:strNotify forKey:kNotificationSetting];
//    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateAppNotification_API];
//    networkManager.delegate = self;
//    [networkManager updateAppNotificationSetting:dict_appSetting  forRequestNumber:2];
//}
- (void)updateBadgeCount:(NSString *)notificationId{
    NSMutableDictionary *dict_appSetting=[[NSMutableDictionary alloc] init];
    [dict_appSetting setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_appSetting setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_appSetting setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_appSetting setSafeObject:notificationId forKey:kNotificationId];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateBadgeCount_API];
    networkManager.delegate = self;
    [networkManager updateAppNotificationSetting:dict_appSetting  forRequestNumber:3];
}
-(void)saveLogInData:(NSDictionary *)dict_data
{
    self.parentInfo=[[Parent alloc] init];
    self.parentInfo.tokenData=[[dict_data safeObjectForKey:kData] safeObjectForKey:kTokenData];
    self.parentInfo.parentUserName=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kUserName];
    self.parentInfo.parentName=[NSString stringWithFormat:@"%@ %@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kFirstName],[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLastName]];
    self.parentInfo.parentFirstName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kFirstName]];
    self.parentInfo.parentLastName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLastName]];
    self.parentInfo.parentLocalPhone=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kphone2];
    self.parentInfo.parentPhone=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kphone1];
    self.parentInfo.parentRelationship = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:KRelationship];
   // self.parentInfo.sitterAboutMe=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safesafeObjectForKey:@"about_me"];
    self.parentInfo.parentThumbImage=[NSURL URLWithString:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kChildThumbImage]];
    self.parentInfo.parentAvailable_credits = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kAvailableCredits];
    self.parentInfo.parentChildCountName = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kChildCount];
    self.parentInfo.parentEmergencyContactName = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kEmergencycontactName];
    self.parentInfo.parentEmergencyPhone = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kEmergencyPhone];
    self.parentInfo.parentEmergencyRelation = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kEmergencyRelation];
    self.parentInfo.parentGurdianName = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kSpouseFirstName];
    self.parentInfo.parentGurdianPhone1 =  [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kSpousePhone1];
    self.parentInfo.parentGurdianPhone2 = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kSpousePhone2];
    self.parentInfo.parentGurdianRelationship = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kSpouseRelation];
    self.parentInfo.parentUserId = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kUserId];
    self.parentInfo.authrizedPaymentProfileId = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kAutherizePaymentId];
    self.parentInfo.profileStatus=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kProfileStatus];
    self.parentInfo.timeZone =  [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kTimeZone];
    if ([[[[dict_data safeObjectForKey:kData]safeObjectForKey:kUserDetail]allKeys]containsObject:kLocalAddress]) {
        self.parentInfo.dict_parentLocalAddress = [[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress];
        self.parentInfo.HotelName = [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kHotelName];
        self.parentInfo.CrossStreet =   [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kCrossStreet];
        self.parentInfo.StreetAddress = [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kStreetAddress];
        self.parentInfo.City =          [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kCity];
        self.parentInfo.State =         [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kState];
        self.parentInfo.stateID=        [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kStateId];
        self.parentInfo.zipCode =       [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kZip];
        self.parentInfo.AddressID =     [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kAddressId];
        self.parentInfo.addressType =   [[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocalAddress] safeObjectForKey:kAddressType];

    }
    
}
-(void)saveChildData:(NSMutableArray*)array_childData
{
    
    [self.array_childRecord removeAllObjects];
    for (int i=0; i<array_childData.count; i++) {
       childrenInfo = [[Children alloc]init];
        NSDictionary *dictChild=[array_childData safeObjectAtIndex:i];
        childrenInfo.childAge = [dictChild objectForKey:kChildAge];
        childrenInfo.childSex = [dictChild safeObjectForKey:kChildSex];
        childrenInfo.childName = [dictChild safeObjectForKey:kChildName];
        childrenInfo.childallergies = [dictChild safeObjectForKey:kchildAlergies];
        childrenInfo.childAllergyStatus =[dictChild safeObjectForKey:kchildAlergiesStatus];
        childrenInfo.childdob = [dictChild safeObjectForKey:kDOB];
        childrenInfo.childFavbook = [dictChild safeObjectForKey:kChildFavBook];
        childrenInfo.childFavCartoon =[dictChild safeObjectForKey:kChildFavCartoon];
        childrenInfo.childfavFood = [dictChild safeObjectForKey:kChildFavFood];
        childrenInfo.childHelpFullHint =[dictChild safeObjectForKey:kChildNotes];
        childrenInfo.childJobId =[dictChild safeObjectForKey:kChildId];
        childrenInfo.childMainImage =[dictChild safeObjectForKey:kChildMainImage];
        childrenInfo.childMedicator =[dictChild safeObjectForKey:kChildMedicator];
        childrenInfo.childMedicatorStatus =[dictChild safeObjectForKey:kChildMedicatorStatus];
        childrenInfo.childOriginalImage =[dictChild safeObjectForKey:kChildOriginalImage];
        childrenInfo.childRelation = [dictChild safeObjectForKey:kChildRelation];
        childrenInfo.childSex = [dictChild safeObjectForKey:kChildSex];
        childrenInfo.childSpecialNeeds =[dictChild safeObjectForKey:kChildSpecialNeeds];
        childrenInfo.childSpecialNeedsStatus =[dictChild safeObjectForKey:kChildSpecialNeedsStatus];
        childrenInfo.childThumbImage = [dictChild safeObjectForKey:kChildThumbImage];
        childrenInfo.childRelationShip = [dictChild safeObjectForKey:kChildRelation];
        childrenInfo.childParentName = [dictChild safeObjectForKey:kRealtionName];
        childrenInfo.childParentContact = [dictChild safeObjectForKey:kRealtionContact];
        
        [self.array_childRecord  addObject:childrenInfo];
        
}
}
-(void)saveJobList:(NSMutableArray *)array_jobList
{
    self.array_jobList = [[NSMutableArray alloc]init];
    for (int i=0; i<array_jobList.count; i++) {
        jobInfo = [[JobList alloc]init];
        jobInfo.actualEndDate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kActualEndDate];
        jobInfo.actualStartDate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kActualStartDate];
        jobInfo.addressId =  [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kAddressId];
        jobInfo.clientUserId = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kClientUserId];
        jobInfo.jobEndDate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kJobEndDate];
        jobInfo.jobStartDate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kJobStartDate];
        jobInfo.jobId = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kJobId];
        jobInfo.jobStatus = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kJobStatus];
        jobInfo.jobPostedDate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kJobPostedDate];
        jobInfo.notes = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kNotes];
        jobInfo.rate = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kRate];
        jobInfo.totalPaid = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kTotalPaid];
        jobInfo.totalHours = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kTotalHours];
       
        jobInfo.sitterFirstName = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kSitterFirstName];
        jobInfo.sitterLastName = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kSitterLastName];
        jobInfo.sitterPhone = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kSitterPhone];
        jobInfo.sitterUserId = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kSitterUserId];
        jobInfo.sitterUserName = [[array_jobList safeObjectAtIndex:i]safeObjectForKey:kSitterUserName];

         [self.array_jobList  addObject:jobInfo];
    }
}
-(void)saveJobDetail:(NSDictionary *)dict_jobDetail
{
    self.array_jobChildDetail = [[NSMutableArray alloc]init];
    self.jobDetail = [[JobList alloc]init];
    self.jobDetail.actualEndDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kActualEndDate];
    self.jobDetail.actualStartDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kActualStartDate];
    self.jobDetail.addressId = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kAddressId];
    self.jobDetail.childCount = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kChildCount];
    self.jobDetail.clientUserId = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kClientUserId];
    self.jobDetail.compleatedDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kCompleatedDate];
    self.jobDetail.firstName = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kFirstName];
    self.jobDetail.lastName = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kLastName];
    self.jobDetail.jobEndDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kJobEndDate];
    self.jobDetail.jobStartDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kJobStartDate];
    self.jobDetail.jobId = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kJobId];
    self.jobDetail.jobStatus = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kJobStatus];
    self.jobDetail.jobPostedDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kJobPostedDate];
    self.jobDetail.lastModifiedDate = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kLastModifiedDate];
    self.jobDetail.notes = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kNotes];
    
    //Address
    NSString *str_localAddress = [NSString stringWithFormat:@""];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kStreetAddress]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kCity]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kState]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@ ",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kZip]]];
    if (![[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kCrossStreet] isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kCrossStreet]]];
    }
    if (![[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kHotelName] isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@",[[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail] objectForKey:kAddress]objectForKey:kHotelName]]];
    }
    self.jobDetail.jobAddress = str_localAddress;
    
    
    
    self.jobDetail.sitterFirstName =  [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kSitterFirstName];
    self.jobDetail.sitterLastName =  [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kSitterLastName];
    self.jobDetail.sitterProfilePic=[[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:ksitterThumbImage];
    self.jobDetail.sitterPhone =  [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kSitterPhone];
    self.jobDetail.sitterUserId = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kSitterUserId];
    self.jobDetail.sitterUserName = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kSitterUserName];
    self.jobDetail.totalAssigned = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kTotalAssigned];
    self.jobDetail.totalPaid = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kTotalPaid];
    
    NSMutableArray  *array_childData = [[[dict_jobDetail safeObjectForKey:kData]safeObjectForKey:kJobDetail]safeObjectForKey:kChildren];
    for (int i=0; i<array_childData.count; i++) {
         childrenInfo = [[Children alloc]init];
        childrenInfo.childAge = [[array_childData safeObjectAtIndex:i]objectForKey:kChildAge];
        childrenInfo.childSex = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildSex];
        childrenInfo.childName = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildName];
        childrenInfo.childallergies = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kchildAlergies];
        childrenInfo.childAllergyStatus =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kchildAlergiesStatus];
        childrenInfo.childdob = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kDOB];
        childrenInfo.childFavbook = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildFavBook];
        childrenInfo.childFavCartoon =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildFavCartoon];
        childrenInfo.childfavFood = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildFavFood];
        childrenInfo.childHelpFullHint =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildNotes];
        childrenInfo.childJobId =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildId];
        childrenInfo.childMainImage =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildMainImage];
        childrenInfo.childMedicator =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildMedicator];
        childrenInfo.childMedicatorStatus =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildMedicatorStatus];
        childrenInfo.childOriginalImage =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildOriginalImage];
        childrenInfo.childRelation = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildRelation];
        childrenInfo.childSex = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildSex];
        childrenInfo.childSpecialNeeds =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildSpecialNeeds];
        childrenInfo.childSpecialNeedsStatus =[[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildSpecialNeedsStatus];
        childrenInfo.childThumbImage = [[array_childData safeObjectAtIndex:i]safeObjectForKey:kChildThumbImage];
        
        [self.array_jobChildDetail  addObject:childrenInfo];
    }
}
-(void)saveSitterDetail:(NSDictionary *)dict_data
{
    self.sitterInfo = [[Sitter alloc]init];
    self.sitterInfo.str_TokenData=[[dict_data safeObjectForKey:kData] safeObjectForKey:kTokenData];
    self.sitterInfo.sitterId=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kUserId]];
    self.sitterInfo.sitterEmail=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kUserName];
    self.sitterInfo.sitterName=[NSString stringWithFormat:@"%@ %@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kFirstName],[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kLastName]];
    self.sitterInfo.sitterFirstName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kFirstName]];
    self.sitterInfo.sitterLastName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kLastName]];
    self.sitterInfo.sitterPhone1=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kphone1];
    self.sitterInfo.sitterPhone2=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kphone2];
    self.sitterInfo.sitterAboutMe=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kAboutMe];
    self.sitterInfo.sitterProfileImageUrl=[NSURL URLWithString:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kChildThumbImage]];
    self.sitterInfo.array_Certificates=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kCertification];
    NSDictionary *dict_preference=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kPreferences];
    if (!dict_preference.count==0)
    {
       self.sitterInfo.array_Area=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kPreferences] safeObjectForKey:kArea];
        self.sitterInfo.array_Child_preferences=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kPreferences] safeObjectForKey:kChildPreferences];
        self.sitterInfo.array_Other_preferences=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kPreferences] safeObjectForKey:kOther];
        self.sitterInfo.array_Language=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kProfileData] safeObjectForKey:kPreferences] safeObjectForKey:kLanguage];
    }
   
}
-(void)callStateListAPI
{
    NSMutableDictionary *dict_countryId = [[NSMutableDictionary alloc]init];
    [dict_countryId setSafeObject:@"223" forKey:kCountryId];
    [dict_countryId setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kStateListAPI];
    DDLogInfo(@"%@",kStateListAPI);
    networkManager.delegate = self;
    [networkManager getStateList:dict_countryId forRequestNumber:1];
}

-(NSString*)setLocalTime:(id)vc withTime:(NSDate *)date andDateFormat:(NSDateFormatter *)dateFormatter
{
    NSTimeZone *currentTimeZone =[NSTimeZone systemTimeZone];
    NSTimeZone *utcTimeZone = [NSTimeZone timeZoneWithAbbreviation:self.parentInfo.timeZone];
    
    NSInteger currentGMTOffset = [currentTimeZone secondsFromGMTForDate:date];
    NSInteger gmtOffset = [utcTimeZone secondsFromGMTForDate:date];
    NSTimeInterval gmtInterval = (currentGMTOffset - gmtOffset);
    DDLogInfo(@"gmt interval %f",gmtInterval);
    NSDate *destinationDate = [[NSDate alloc] initWithTimeInterval:gmtInterval sinceDate:date];
    NSDateFormatter *dateFormatters = [[NSDateFormatter alloc] init];
    //[dateFormatters setDateFormat:@"MM/dd/yyyy '-' hh:mm a"];
    [dateFormatters setTimeZone:[NSTimeZone localTimeZone]];
    NSString *dateStr = [dateFormatter stringFromDate: destinationDate];
    NSLog(@"DateString : %@", dateStr);
    return dateStr;
}
// Create image from color
+(UIImage *)imageWithColor:(UIColor *)color
{
    CGRect rect = CGRectMake(0.0f, 0.0f, 1.0f, 1.0f);
    UIGraphicsBeginImageContext(rect.size);
    CGContextRef context = UIGraphicsGetCurrentContext();
    
    CGContextSetFillColorWithColor(context, [color CGColor]);
    CGContextFillRect(context, rect);
    
    UIImage *image = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    return image;
}
#pragma mark - SMCoreNetworkManagerDelegate
- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
   
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                
                array_statelist = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kStateList];
                [ApplicationManager getInstance].array_stateList = [array_statelist mutableCopy];
                DDLogInfo(@"State list is %@",array_statelist);
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
//        case 2:
//            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
//                [ApplicationManager getInstance].sitterInfo.appNotificationSetting=[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:kNotificationSetting];
//            }else
//            {
//                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
//            }
//            
//            break;
        case 3:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [UIApplication sharedApplication].applicationIconBadgeNumber=[[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kBadgeCount] intValue];
                DDLogInfo(@"Badge count %d",[[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kBadgeCount] intValue]);
                
            }else
            {
                //[[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
            
        default:
            break;
    }

    
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    DDLogInfo(@"%@",error);
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription]];
    
}
@end



