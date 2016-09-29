 //Constants.h
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//



#pragma mark All Constant

#define SYSTEM_VERSION_GREATER_THAN_OR_EQUAL_TO(v)  ([[[UIDevice currentDevice] systemVersion] compare:v options:NSNumericSearch] != NSOrderedAscending)

#define iOS8 SYSTEM_VERSION_GREATER_THAN_OR_EQUAL_TO(@"8.0")

//#define UIColorFromHexCode(rgbValue) [UIColor colorWithRed:((float)((rgbValue & 0xFF0000) >> 16))/255.0 green:((float)((rgbValue & 0xFF00) >> 8))/255.0 blue:((float)(rgbValue & 0xFF))/255.0 alpha:0.80]
#define UIColorFromHexCode(rgbValue) [UIColor colorWithRed:((float)((rgbValue & 0xFF0000) >> 16))/255.0 green:((float)((rgbValue & 0xFF00) >> 8))/255.0 blue:((float)(rgbValue & 0xFF))/255.0 alpha:1.0]

#define kKeyboardAppearedObserver [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWasShown:) name:UIKeyboardDidShowNotification object:nil];

#define kKeyboardHiddenObserver [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillBeHidden:) name:UIKeyboardWillHideNotification object:nil];

#define kAppDelegate (AppDelegate*)[[UIApplication sharedApplication]delegate]


#define kiPhone5Position(h) [[UIScreen mainScreen] bounds].size.height>480 ? h+88 : h
#define CELL_FONT  [UIFont fontWithName:@"HelveticaNeue-Medium" size:16.0]//[UIFont boldSystemFontOfSize:16]
#define CELL_TEXT_COLOR      [UIColor darkGrayColor]


//for setting the color
#define kBackgroundColor [UIColor colorWithRed:243.0/255.0 green:243.0/255.0 blue:243.0/255.0 alpha:1.0]
#define kColorGrayLight [UIColor colorWithRed:102.0/255.0 green:102.0/255.0 blue:102.0/255.0 alpha:1.0]
#define kColorGrayDark [UIColor colorWithRed:51.0/255.0 green:51.0/255.0 blue:51.0/255.0 alpha:1.0]
#define kColorWhite [UIColor whiteColor]
#define kColorAppGreen [UIColor colorWithRed:35.0/255.0 green:146.0/255.0 blue:87.0/255.0 alpha:1.0]

#define kNavigationBarColor [UIColor colorWithRed:(255/255.0) green:(27/255.0) blue:(132/255.0) alpha:1]


//
#define kName @"name"
#define kRole @"role"
#define kEmail @"email"
#define kUserName @"username"
#define kProfile_pic @"profile_pic"
#define kImages @"images"
#define kThumbImage @"thumb_image"
#define kOrginalImage @"orginal_image"
#define kPassword @"password"
#define kAge @"age"
#define kSex @"sex"
#define kUserId @"userid"
#define kAPI_Key @"api_key"
#define kUserDetail @"user_detail"
#define kData @"data"
#define kLogedinUserDetail @"logedinUserDetail"
#define kTokenData @"token_data"
#define kToken @"token"
#define kTimeZone @"timezone"
#define kUserStateZone @"userStateZone"
#define kUpdateChildCount @"updateChildCount"


#define kProfilePic @"profile_pic"
#define kIs_Flagged @"is_flagged"
#define kJobStatus @"job_status"
#define kPending @"pending"
#define kCompleted @"completed"
#define kConfirmed @"confirmed"
#define kOpenJob @"OpenJob"
#define kActiveJob @"active"
#define kClosedJob @"closed"
#define kCancelledJob @"cancelled"
#define kScheduledJob @"ScheduledJob"
#define kCompletedJob @"CompletedJob"
#define kJobList @"jobList"
#define kJobId @"job_id"
#define kJob_status @"job_status"
#define kJobStartDate @"job_start_date"
#define kCurrentPassword @"current_password"
#define kFirstName @"firstname"
#define kLastName @"lastname"
#define kPhone @"phone"
#define kLocal_Phone @"local_phone"
#define kAbout_Me @"about_me"
#define kTotalEarned @"total_earned"
#define kTotalOwned @"total_owed"
#define kTotalPaid  @"total_paid"
#define kParentInfo @"parent_info"
#define kEmergencyContact @"emergency_contact"
#define kEmergencyPhone @"emergency_phone"
#define kGuardianName @"spouse_firstname"
#define kGuardianPhone1 @"spouse_phone1"
#define kGuardianPhone2 @"spouse_phone2"
#define kRelationship @"emergency_relation"
#define kStatus @"status"
#define kErrorDisplayMessage @"errorDisplayMessage"
#define kErrorCode @"errorCode"
#define kStatusSuccess @"success"
#define kStatusFailed @"failed"
#define kFL2 @"FL2"

#define kMobileNumber @"mobile"
#define kAddress @"address"
#define kAddress1 @"address_1"
#define kHotelName           @"billing_name"
#define kStreetAddress  @"streat_address"
#define kChildren @"children"
#define kMessage @"message"
#define kCity @"city"
#define kState @"state"
#define kZipCode @"zipcode"
#define DEVICE_TOKEN @"deviceToken"
#define kDeviceId @"device_id"

#define TIMEOUT_INTERVAL 60.0

#define kCertType @"certificateType"
#define kCertBoolValue @"certificateBoolValue"
#define kCertDate @"certificateDate"
#define kCertifications @"certifications"
#define kSitterPreferList @"sitterPreferList"
#define kChildPreferences @"child_preferences"
#define kArea @"area"
#define kLanguage @"language"
#define kOther @"other"
#define kOtherCert @"Special Needs Experiences SN"
#define kPreferences @"preferences"
#define kIsSelected @"is_selected"
#define kJobEndDate @"job_end_date"
#define kChildCount @"child_count"
#define kRate @"rate"
#define kActual_end_date @"actual_end_date"
#define kPayment_Status @"payment_status"
#define kTotal_paid @"total_paid"
#define kDate @"date"
#define kKey1 @"key1"
#define kKey2 @"key2"
#define kPreferId @"prefer_id"
#define kPreferName @"prefer_name"
#define kNotificationSetting @"notify"
#define kNotificationId @"notification_id"
#define kTotalHours @"total_hours"
#define kActual_start_date @"actual_start_date"
#define kActual_end_date @"actual_end_date"
#define kActual_child_count @"actual_child_count"
#define kNote_aboutJob @"notes"
#define kJobSpecialNeed @"special_need"

#define kAddtionalChildId @"child_id"
// For Pagination
#define kPageCount @"page_count"
#define kPagination @"pagination"
//Additional information
#define kCertification @"certification"
#define kArrayCertAndTraining @"array_CertAndTraining"
#define kArrayArea @"array_Area"
#define kArrayChildPreferences @"array_ChildPreferences"
#define kArrayLanguage @"array_Language"
#define kArrayOtherPreferences @"array_OtherPreferences"
#define kNotificationSelectedAdditionaInfo @"SetSelectedAdditionaInfo"
#define kOtherPreferences @"otherpreference"

// Child keys
#define kChildName            @"child_name"
#define kChildDOB             @"child_dob"
#define kDOB                  @"dob"
#define kchildAlergiesStatus  @"allergy_status"
#define kchildAlergies        @"allergies"
#define kChildMedicatorStatus @"medicator_status"
#define kChildMedicator       @"medicator"
#define kChildFavBook         @"fav_book"
#define kChildFavCartoon      @"fav_cartoon"
#define kChildFavFood         @"fav_food"
#define kChildSpecialNeeds    @"special_needs"
#define kChildSpecialNeedsStatus @"special_needs_status"
#define kChildNotes           @"notes"
#define kChildProfile         @"child_profile"
#define kChildPic             @"child_pic"
#define kChildList            @"child_list"
#define kOriginalImage        @"orginal_image"
#define kChildId              @"child_id"
#define kChildParentUserId    @"parent_user_id"
#define kChildAge             @"age"
#define kChildMainImage       @"main_image"
#define kChildOriginalImage   @"orginal_image"
#define kChildThumbImage      @"thumb_image"
#define kChildRelation        @"child_relation"
#define kChildSex             @"sex"
#define kPicData              @"pic_data"
#define kisSpecialNeed        @"is_special"
#define kRealtionName      @"parent_name"
#define kRealtionContact     @"parent_contact"
#define kNotificationSitterStatus  @"sitterStatus"
//Key for notification
#define kAPS @"aps"
#define kNotoficationAlert @"alert"
#define kBadgeCount @"badge"
#define kNotificationType @"notification_type"
#define kAddChildNotification @"AddChild"

//Font
#define kOptionListFont [UIFont boldSystemFontOfSize:15]
#define kSystemFont [UIFont systemFontOfSize:13]
#define Font_Roboto_bold @"Roboto-Bold"
#define Font_Roboto_light @"RobotoCondensed-Regular" //@"Roboto-Light"
#define Font_RobotoCondensed_Regular @"RobotoCondensed-Regular"

#define Roboto_Medium @"Roboto-Medium"
#define Roboto_Regular @"Roboto-Regular"
#define Roboto_Light @"Roboto-Light"
#define FontSizeForRobotoLight 14.0
#define FontSizeForRobotoBold 14.0
#define FontSizeForRobotoRegular 14.0
#define FontSizeForChildDetail 13.0
#define FontSize16 16.0
#define FontSize13 13.0
#define FontSize12 12.0
//Number Foramt
#define kNumberFormat @"US"

#define kAddSettingButtonForNavigation  UIImage *backImage = [UIImage imageNamed:@"setting-icon"];UIButton *btnSetting = [UIButton buttonWithType:UIButtonTypeCustom];btnSetting.bounds = CGRectMake( 0, 0, 20, 20 );btnSetting.frame = CGRectMake(0, 0, 20, 20 ); [btnSetting setImage:backImage forState:UIControlStateNormal];[btnSetting setContentHorizontalAlignment: isiOS7?UIControlContentHorizontalAlignmentLeft:UIControlContentHorizontalAlignmentCenter];[btnSetting addTarget:self action:@selector(onClickedMenu:) forControlEvents:UIControlEventTouchUpInside];UIBarButtonItem *btnMenu = [[UIBarButtonItem alloc] initWithCustomView:btnSetting]; self.navigationItem.rightBarButtonItem=btnMenu;

#define kAddSaveBarButtonForNavigation UIBarButtonItem *btnSaveMenu = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemSave target:self action:@selector(onClickedSave:)];self.navigationItem.rightBarButtonItem=btnSaveMenu;

#define kAddDoneBarButtonForNavigation UIBarButtonItem *btnSaveMenu = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone target:self action:@selector(onClickedDone:)];self.navigationItem.rightBarButtonItem=btnSaveMenu;


#define isiOS7 [[UIDevice currentDevice].systemVersion floatValue]>=7.0 ? YES : NO
#define kWindowWidth [UIScreen mainScreen].bounds.size.width

#pragma mark trimed string
inline static NSString *trimedString(NSString *str){
    NSString *trimmedString = [str stringByTrimmingCharactersInSet:
                               [NSCharacterSet whitespaceAndNewlineCharacterSet]];
    return trimmedString;
}
inline static UILabel *labelWithFrame(CGRect frm){
      UILabel *lblTemp=[[UILabel alloc]initWithFrame:frm];
    [lblTemp setFont:kSystemFont];
    return lblTemp;
}
