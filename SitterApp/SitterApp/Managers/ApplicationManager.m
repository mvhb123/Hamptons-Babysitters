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
        self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:kNumberFormat];
        self.sitterInfo=[[Sitter alloc] init];
        self.jobList=[[JobList alloc] init];
        self.sitterAdditionInfo=[[SitterAdditionInformation alloc] init];

    }
    return self;
}
# pragma mark- Method for showing generic alert
- (void)showAlertForVC:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
   
         //Show Alert with title and message only
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
}
-(void)saveLogInData:(NSDictionary *)dict_data
{
    self.sitterInfo.str_TokenData=[[dict_data safeObjectForKey:kData] safeObjectForKey:kTokenData];
    self.sitterInfo.sitterId=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kUserId]];
    self.sitterInfo.sitterStatus=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:@"status"]];
    self.sitterInfo.sitterEmail=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kUserName];
    self.sitterInfo.sitterName=[NSString stringWithFormat:@"%@ %@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kFirstName],[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLastName]];
    self.sitterInfo.sitterFirstName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kFirstName]];
    self.sitterInfo.sitterLastName=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLastName]];
    self.sitterInfo.sitterPhone1=[self.numFormatter formatText:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPhone]] ;
    self.sitterInfo.sitterPhone2=[self.numFormatter formatText:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kLocal_Phone]];
    self.sitterInfo.sitterAboutMe=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kAbout_Me];
    self.sitterInfo.sitterProfileImageUrl=[NSURL URLWithString:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kThumbImage]];
    self.sitterInfo.sitterProfileOriginalImageUrl=[NSURL URLWithString:[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kOrginalImage]];
    
    self.sitterInfo.array_Certificates=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kCertification];
    self.sitterInfo.appNotificationSetting=[NSString stringWithFormat:@"%@",[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kNotificationSetting]];
    self.sitterInfo.str_OtherPreferences=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kOtherPreferences];
    
    NSDictionary *array_preference=[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPreferences];
    if (!array_preference.count==0)
    {
        self.sitterInfo.array_Area=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPreferences] safeObjectForKey:kArea];
        self.sitterInfo.array_Child_preferences=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPreferences] safeObjectForKey:kChildPreferences];
        self.sitterInfo.array_Other_preferences=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPreferences] safeObjectForKey:kOther];
        self.sitterInfo.array_Language=[[[[dict_data safeObjectForKey:kData] safeObjectForKey:kUserDetail] safeObjectForKey:kPreferences] safeObjectForKey:kLanguage];
    }
    
    
}

-(void)saveAdditionalInformation:(NSDictionary *)dict_data
{
    self.sitterAdditionInfo.array_CertAndTraining = [[NSMutableArray alloc] initWithArray:[[[dict_data safeObjectForKey:kData]  safeObjectForKey:kCertifications]mutableCopy]];
    self.sitterAdditionInfo.array_Area = [[NSMutableArray alloc] initWithArray:[[[[dict_data safeObjectForKey:kData]  safeObjectForKey:kSitterPreferList] safeObjectForKey:kArea]mutableCopy]];
    self.sitterAdditionInfo.array_ChildPreferences = [[NSMutableArray alloc] initWithArray:[[[[dict_data safeObjectForKey:kData]  safeObjectForKey:kSitterPreferList] safeObjectForKey:kChildPreferences]mutableCopy]];
    self.sitterAdditionInfo.array_Language = [[NSMutableArray alloc] initWithArray:[[[[dict_data safeObjectForKey:kData]  safeObjectForKey:kSitterPreferList] safeObjectForKey:kLanguage]mutableCopy]];
    self.sitterAdditionInfo.array_OtherPreferences = [[NSMutableArray alloc] initWithArray:[[[[dict_data safeObjectForKey:kData]  safeObjectForKey:kSitterPreferList] safeObjectForKey:kOther]mutableCopy]];
 
}



-(void)saveJobList:(NSDictionary *)dict_data andJobType:(NSString*)jobType
{
    NSDictionary *dict_Pagination=[[dict_data objectForKey:kData] objectForKey:kPagination];
    if ([jobType isEqualToString:kOpenJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_OpenJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_OpenJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
    }else if ([jobType isEqualToString:kScheduledJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_ScheduledJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_ScheduledJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
        
      
    }else if ([jobType isEqualToString:kActiveJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_ActiveJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_ActiveJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
        
        
    }
    else if ([jobType isEqualToString:kCompletedJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_CompletedJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_CompletedJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
    }else if ([jobType isEqualToString:kClosedJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_ClosedJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_ClosedJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
    }else if ([jobType isEqualToString:kCancelledJob]){
        if ([[dict_Pagination safeObjectForKey:@"page"] isEqualToString:@"1"]) {
            self.jobList.array_CancelledJob = [[NSMutableArray alloc] initWithArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }else{
            [self.jobList.array_CancelledJob addObjectsFromArray:[[dict_data safeObjectForKey:kData] safeObjectForKey:kJobList]];
        }
    }
}

//-------------------------------------------------------------------------------
//method is used for changing the format of date:
//-------------------------------------------------------------------------------
-(NSString *)convertDateFormate:(NSString *)str_date andDateFormate:(NSString *)dateFormate
{
    NSString *dateStr =str_date;
    NSDateFormatter *dateFormatter1 = [[NSDateFormatter alloc] init];
    [dateFormatter1 setDateFormat:dateFormate];
    NSDate *date = [dateFormatter1 dateFromString:dateStr];
    NSTimeZone *currentTimeZone = [NSTimeZone localTimeZone];
    NSTimeZone *edtTimeZone = [NSTimeZone timeZoneWithAbbreviation:self.sitterInfo.str_TimeZone];
    NSInteger currentGMTOffset = [currentTimeZone secondsFromGMTForDate:date];
    NSInteger gmtOffset = [edtTimeZone secondsFromGMTForDate:date];
    NSTimeInterval gmtInterval = currentGMTOffset - gmtOffset;
    NSDate *destinationDate = [[NSDate alloc] initWithTimeInterval:gmtInterval sinceDate:date];
    // return destinationDate;
    NSDateFormatter *dateFormatters = [[NSDateFormatter alloc] init];
    [dateFormatters setDateFormat:@"mm/dd/yy hh a"];
    [dateFormatters setTimeZone:[NSTimeZone localTimeZone]];
    dateStr = [dateFormatters stringFromDate: destinationDate];
    return dateStr;
}


#pragma mark-APICalling
- (void)logOutAPI:(NSMutableDictionary *)dict_logOut{
    NSMutableDictionary *dict_LogOut=[[NSMutableDictionary alloc] init];
    [dict_LogOut setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_LogOut setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_LogOut setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kLogOut_API];
    networkManager.delegate = self;
    [networkManager logOut:dict_LogOut forRequestNumber:1];
}
- (void)updateAppNotificationSeting:(NSString *)strNotify{
    NSMutableDictionary *dict_appSetting=[[NSMutableDictionary alloc] init];
    [dict_appSetting setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_appSetting setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_appSetting setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_appSetting setSafeObject:strNotify forKey:kNotificationSetting];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateAppNotification_API];
    networkManager.delegate = self;
    [networkManager updateAppNotificationSetting:dict_appSetting  forRequestNumber:2];
}
- (void)updateBadgeCount:(NSString *)notificationId{
    NSMutableDictionary *dict_appSetting=[[NSMutableDictionary alloc] init];
    [dict_appSetting setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_appSetting setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_appSetting setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
   [dict_appSetting setSafeObject:notificationId forKey:kNotificationId];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateBadgeCount_API];
    networkManager.delegate = self;
    [networkManager updateAppNotificationSetting:dict_appSetting  forRequestNumber:3];
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [[NSNotificationCenter defaultCenter] postNotificationName:@"HIDE_ACTIVITY_INDICATOR" object:nil];
    
    switch (requestId) {
        case 1:
           
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess])
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj safeObjectForKey:kMessage]];
                [UIApplication sharedApplication].applicationIconBadgeNumber = 0;
                //[[NSUserDefaults standardUserDefaults] removeObjectForKey:DEVICE_TOKEN];
               // [ApplicationManager getInstance].sitterInfo=nil;//Reset logedin user info
                [kAppDelegate setRootViewAfterLogout];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
               NSString *strNotificationSetting=[NSString stringWithFormat:@"%@",[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:kNotificationSetting]];
                [ApplicationManager getInstance].sitterInfo.appNotificationSetting=strNotificationSetting;
                NSUserDefaults *userDefault=[NSUserDefaults standardUserDefaults];

                NSMutableDictionary *dictUserData=[[NSMutableDictionary alloc] init];
                [dictUserData addEntriesFromDictionary:[[NSUserDefaults standardUserDefaults] objectForKey:kLogedinUserDetail]];
               
                NSMutableDictionary *tempDict=[[NSMutableDictionary alloc] init];
                [tempDict addEntriesFromDictionary:[[dictUserData objectForKey:kData] objectForKey:kUserDetail]];
                
                NSMutableDictionary *tempDictData=[[NSMutableDictionary alloc] init];
                [tempDictData addEntriesFromDictionary:[dictUserData objectForKey:kData]];
                [tempDict setObject:strNotificationSetting forKey:kNotificationSetting];
                [tempDictData setObject:tempDict forKey:kUserDetail];
                [dictUserData setObject:tempDictData forKey:kData];
                
              [userDefault removeObjectForKey:kLogedinUserDetail];
              [userDefault setObject:dictUserData forKey:kLogedinUserDetail];
              [userDefault synchronize];
                
         
            }else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
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
    [[NSNotificationCenter defaultCenter] postNotificationName:@"HIDE_ACTIVITY_INDICATOR" object:nil];
    // NSError *errorcode=(NSError *)error;
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark - view for child scroll view
-(UIView*)createViewForchildInfo:(NSDictionary*)dictChildInfo frame:(CGRect)frm{
    UIView *viewChild=[[UIView alloc]initWithFrame:frm];
    float xPos1=25;
    AsyncImageView *imgChildProfile=[[AsyncImageView alloc]initWithFrame:CGRectMake(xPos1, 5, 80, 80)];
    //[imgChildProfile.layer setCornerRadius:40];
    //[imgChildProfile setClipsToBounds:YES];
    [imgChildProfile loadImageFromURL:[NSURL URLWithString:[dictChildInfo objectForKey:kThumbImage]]];
    [viewChild setBackgroundColor:kColorWhite];
    [viewChild addSubview:imgChildProfile];
    xPos1=xPos1+imgChildProfile.frame.size.width+5;
    float xPos=xPos1+100;
    float yPos=5;
    //Child name
    UILabel *lbl_childName=[self getEmptyLabel];
    [lbl_childName setFrame:CGRectMake(xPos1, yPos, 100, 15)];
    [lbl_childName setText:@"Child Name "];
    [lbl_childName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childName setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childName];
    
    // Child name value
    float lblWidth=95;//imgChildProfile.frame.origin.x-lbl_childName.frame.size.width-5;
    UILabel *lbl_childNameValue=[self getEmptyLabel];
    [lbl_childNameValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
    [lbl_childNameValue setText:[dictChildInfo safeObjectForKey:@"child_name"]];
    [lbl_childNameValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_childNameValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childNameValue];
    yPos=lbl_childNameValue.frame.size.height+lbl_childNameValue.frame.origin.y+5;
    //Child age
    UILabel *lbl_childAge=[self getEmptyLabel];
    [lbl_childAge setFrame:CGRectMake(xPos1, yPos, 100, 15)];
    [lbl_childAge setText:@"Age"];
    [lbl_childAge setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childAge setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childAge];
    // child age value
    //lblWidth=120;//imgChildProfile.frame.origin.x-lbl_childAge.frame.size.width-5;
    UILabel *lbl_childAgeValue=[self getEmptyLabel];
    [lbl_childAgeValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
    NSString *str_age=[dictChildInfo safeObjectForKey:@"age"];
  
    [lbl_childAgeValue setText:str_age];
    [lbl_childAgeValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_childAgeValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childAgeValue];
    yPos=lbl_childAgeValue.frame.size.height+lbl_childAgeValue.frame.origin.y+5;
    
    //Child Special Need
    UILabel *lbl_childSpNeed=[self getEmptyLabel];
    [lbl_childSpNeed setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_childSpNeed setText:@"Special needs"];
    [lbl_childSpNeed setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childSpNeed setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childSpNeed];
    
    if ([[[dictChildInfo safeObjectForKey:@"special_needs_status"] lowercaseString] isEqualToString:@"yes"]) {
        // child Special Need value
        UILabel *lbl_childSpNeedValue=[self getEmptyLabel];
        [lbl_childSpNeedValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childSpNeedValue setText:[dictChildInfo safeObjectForKey:@"special_needs"]];
        [lbl_childSpNeedValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childSpNeedValue setTextColor:kColorGrayDark];
        [viewChild addSubview:lbl_childSpNeedValue];
        yPos=lbl_childSpNeedValue.frame.size.height+lbl_childSpNeedValue.frame.origin.y+5;
    }else{
        // child Special Need value
        UILabel *lbl_childSpNeedValue=[self getEmptyLabel];
        [lbl_childSpNeedValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childSpNeedValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childSpNeedValue setTextColor:kColorGrayDark];
        [lbl_childSpNeedValue setText:[dictChildInfo safeObjectForKey:@"special_needs_status"]];
        [viewChild addSubview:lbl_childSpNeedValue];
        yPos=lbl_childSpNeedValue.frame.size.height+lbl_childSpNeedValue.frame.origin.y+5;
        
    }
    //Child allergy_status
    UILabel *lbl_childAllergy=[self getEmptyLabel];
    [lbl_childAllergy setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_childAllergy setText:@"Allergies "];
    [lbl_childAllergy setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childAllergy setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childAllergy];
   // lblWidth=imgChildProfile.frame.origin.x-lbl_childAllergy.frame.size.width-5;
    
    if ([[[dictChildInfo safeObjectForKey:@"allergy_status"] lowercaseString] isEqualToString:@"yes"]) {
        // child allergy_status value
        UILabel *lbl_childAllergyValue=[self getEmptyLabel];
        [lbl_childAllergyValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childAllergyValue setText:[dictChildInfo safeObjectForKey:@"allergies"]];
        [lbl_childAllergyValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childAllergyValue setTextColor:kColorGrayDark];
        [viewChild addSubview:lbl_childAllergyValue];
        yPos=lbl_childAllergyValue.frame.size.height+lbl_childAllergyValue.frame.origin.y+5;
        
    }else{
        // child allergy_status value
        UILabel *lbl_childAllergyValue=[self getEmptyLabel];
        [lbl_childAllergyValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childAllergyValue setText:[dictChildInfo safeObjectForKey:@"allergy_status"]];
        [lbl_childAllergyValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childAllergyValue setTextColor:kColorGrayDark];
        [viewChild addSubview:lbl_childAllergyValue];
        yPos=lbl_childAllergyValue.frame.size.height+lbl_childAllergyValue.frame.origin.y+5;
    }
    //Child medicator_status
    //xPos1=25;
    //xPos=230;
    UILabel *lbl_childmedicator=[self getEmptyLabel];
    [lbl_childmedicator setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_childmedicator setText:@"Medication "];
    [lbl_childmedicator setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_childmedicator setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_childmedicator];
    //lblWidth=imgChildProfile.frame.origin.x-lbl_childmedicator.frame.size.width-5;
    
    if ([[[dictChildInfo safeObjectForKey:@"medicator_status"] lowercaseString] isEqualToString:@"yes"]) {
        
        // child medicator_status value
        UILabel *lbl_childmedicatorValue=[self getEmptyLabel];
        [lbl_childmedicatorValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childmedicatorValue setText:[dictChildInfo safeObjectForKey:@"medicator"]];
        [lbl_childmedicatorValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childmedicatorValue setTextColor:kColorGrayDark];
        [viewChild addSubview:lbl_childmedicatorValue];
        yPos=lbl_childmedicatorValue.frame.size.height+lbl_childmedicatorValue.frame.origin.y+5;
        
    }else{
        // child medicator_status value
        UILabel *lbl_childmedicatorValue=[self getEmptyLabel];
        [lbl_childmedicatorValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
        [lbl_childmedicatorValue setText:[dictChildInfo safeObjectForKey:@"medicator_status"]];
        [lbl_childmedicatorValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
        [lbl_childmedicatorValue setTextColor:kColorGrayDark];
        [viewChild addSubview:lbl_childmedicatorValue];
        yPos=lbl_childmedicatorValue.frame.size.height+lbl_childmedicatorValue.frame.origin.y+5;
    }
    xPos1=25;
    xPos=xPos1+120;
    lblWidth=lblWidth+50;
    //Child Favorite food
    UILabel *lbl_favFood=[self getEmptyLabel];
    [lbl_favFood setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_favFood setText:@"Favorite Food"];
    [lbl_favFood setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_favFood setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favFood];
    //lblWidth=imgChildProfile.frame.origin.x-lbl_favFood.frame.size.width-5;
    //lblWidth=lblWidth+80;// Add imageview width
    // child Favorite food value
    UILabel *lbl_favFoodValue=[self getEmptyLabel];
    [lbl_favFoodValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
    if ([[dictChildInfo safeObjectForKey:@"fav_food"] isEqualToString:@""]) {
        [lbl_favFoodValue setText:@"Not specified"];
    }else{
        [lbl_favFoodValue setText:[dictChildInfo safeObjectForKey:@"fav_food"]];
    }
    
    [lbl_favFoodValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_favFoodValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favFoodValue];
    yPos=lbl_favFoodValue.frame.size.height+lbl_favFoodValue.frame.origin.y+5;
    
    
    //Child Favorite cartoon
    UILabel *lbl_favCartoon=[self getEmptyLabel];
    [lbl_favCartoon setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_favCartoon setText:@"Favorite Cartoon "];
    [lbl_favCartoon setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_favCartoon sizeToFit];
    [lbl_favCartoon setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favCartoon];
    
    
    // Child Favorite cartton value
    UILabel *lbl_favCartoonValue=[self getEmptyLabel];
    [lbl_favCartoonValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
    if ([[dictChildInfo safeObjectForKey:@"fav_cartoon"] isEqualToString:@""]) {
        [lbl_favCartoonValue setText:@"Not specified"];
    }else{
        [lbl_favCartoonValue setText:[dictChildInfo safeObjectForKey:@"fav_cartoon"]];
    }
    [lbl_favCartoonValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_favCartoonValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favCartoonValue];
    yPos=lbl_favCartoonValue.frame.size.height+lbl_favCartoonValue.frame.origin.y+5;
    
    //Child Favorite Book
    UILabel *lbl_favBook=[self getEmptyLabel];
    [lbl_favBook setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_favBook setText:@"Favorite Book "];
    [lbl_favBook setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_favBook setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favBook];
    
   
    
    // child Favorite book value
    UILabel *lbl_favBookValue=[self getEmptyLabel];
    [lbl_favBookValue setFrame:CGRectMake(xPos, yPos, lblWidth, 15)];
    if ([[dictChildInfo safeObjectForKey:@"fav_book"] isEqualToString:@""]) {
        [lbl_favBookValue setText:@"Not specified"];
    }else{
        [lbl_favBookValue setText:[dictChildInfo safeObjectForKey:@"fav_book"]];
    }
    [lbl_favBookValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_favBookValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_favBookValue];
    yPos=lbl_favBookValue.frame.size.height+lbl_favBookValue.frame.origin.y+5;
    //Child Helpfull hint
    UILabel *lbl_helpfullHint=[self getEmptyLabel];
    [lbl_helpfullHint setFrame:CGRectMake(xPos1,yPos, 100, 15)];
    [lbl_helpfullHint setText:@"Helpful Hint"];
    [lbl_helpfullHint setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_helpfullHint setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_helpfullHint];
    
  
    
    // child Favorite book value
    UILabel *lbl_helpfullHintValue=[self getEmptyLabel];
    [lbl_helpfullHintValue setFrame:CGRectMake(xPos, yPos, lblWidth+20, 15)];
    if ([[dictChildInfo safeObjectForKey:@"notes"] isEqualToString:@""]) {
        [lbl_helpfullHintValue setText:@"Not specified"];
    }else{
        [lbl_helpfullHintValue setText:[dictChildInfo safeObjectForKey:@"notes"]];
    }
    [lbl_helpfullHintValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
   // [lbl_helpfullHintValue sizeToFit];
    [lbl_helpfullHintValue setTextColor:kColorGrayDark];
    [viewChild addSubview:lbl_helpfullHintValue];
    
    
    
    
    return viewChild;
}
-(UILabel*)getEmptyLabel{
    UILabel *lbl=[[UILabel alloc]init];
    //[lbl setFont:kSystemFont];
    return lbl;
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
@end


//add image on navigation bar
