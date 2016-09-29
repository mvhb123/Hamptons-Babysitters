 //Constants.h
//  SitterApp
//
//  Created by Vikram gour on 01/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//



#pragma mark All Constant

#define SYSTEM_VERSION_GREATER_THAN_OR_EQUAL_TO(v)  ([[[UIDevice currentDevice] systemVersion] compare:v options:NSNumericSearch] != NSOrderedAscending)

#define iOS8 SYSTEM_VERSION_GREATER_THAN_OR_EQUAL_TO(@"8.0")
#define IS_OS_7    ([[[UIDevice currentDevice] systemVersion] floatValue] >= 7.0 && [[[UIDevice currentDevice] systemVersion] floatValue] < 8.0)
#define UIColorFromHexCode(rgbValue) [UIColor colorWithRed:((float)((rgbValue & 0xFF0000) >> 16))/255.0 green:((float)((rgbValue & 0xFF00) >> 8))/255.0 blue:((float)(rgbValue & 0xFF))/255.0 alpha:1.0]

#define kKeyboardAppearedObserver [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWasShown:) name:UIKeyboardDidShowNotification object:nil];

#define kKeyboardHiddenObserver [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillBeHidden:) name:UIKeyboardWillHideNotification object:nil];

#define kAppDelegate (AppDelegate*)[[UIApplication sharedApplication]delegate]


#define kiPhone5Position(h) [[UIScreen mainScreen] bounds].size.height>480 ? h+88 : h
#define kdecideContentSize(h) [[UIScreen mainScreen] bounds].size.height>480 ? h-70 : h
#define isiPhone4  ([[UIScreen mainScreen] bounds].size.width <=480)?TRUE:FALSE
#define kScreenHeight [[UIScreen mainScreen] bounds].size.height
#define kScreenWidth [[UIScreen mainScreen] bounds].size.width
//#define CELL_FONT  [UIFont fontWithName:@"HelveticaNeue-Medium" size:20.0]//[UIFont boldSystemFontOfSize:16]
#define CELL_TEXT_COLOR      [UIColor lightGrayColor]


//for setting the Color
#define kColorGrayLight [UIColor colorWithRed:102.0/255.0 green:102.0/255.0 blue:102.0/255.0 alpha:1.0]
#define kColorGrayDark [UIColor colorWithRed:87.0/255.0 green:87.0/255.0 blue:87.0/255.0 alpha:1.0]
#define kColorWhite [UIColor whiteColor]
#define kColorAppGreen [UIColor colorWithRed:35.0/255.0 green:146.0/255.0 blue:87.0/255.0 alpha:1.0]
#define kHeadingColor [UIColor colorWithRed:206.0/255.0 green:206.0/255.0 blue:206.0/255.0 alpha:1.0]
#define kLabelColor [UIColor colorWithRed:51.0/255.0 green:51.0/255.0 blue:51.0/255.0 alpha:1.0]
#define kColorAppBlue [UIColor colorWithRed:11.0/255.0 green:0.0/255.0 blue:120.0/255.0 alpha:1.0]
#define kColorAppDarkBlue [UIColor colorWithRed:0.0/255.0 green:14.0/255.0 blue:90.0/255.0 alpha:1.0]

//Font
//for setting the font
#define RobotoCondensedFont @"RobotoCondensed-Regular"
#define RobotoLightfont @"Roboto-Light"
#define RobotoBoldFont @"Roboto-Bold"
#define RobotoRegularFont @"Roboto-Regular"
#define RobotoMediumFont @"Roboto-Medium"
#define LabelFieldFontSize 12.0
#define TextFieldFontSize 14
#define HeadingFieldFontSize 20
#define ButtonFieldFontSize 16.0
#define kOptionListFont [UIFont boldSystemFontOfSize:15]
#define kSystemFont [UIFont systemFontOfSize:15]



#define kViewBackGroundColor [UIColor colorWithRed:243.0/255.0 green:243.0/255.0 blue:243.0/255.0 alpha:1.0]
#define kBackgroundColor [UIColor colorWithRed:239 green:239 blue:244 alpha:0];
#define kfacebookToken @"facebook_Token"

//
#define MENU_POPOVER_FRAME  CGRectMake(self.view.frame.size.width-215, 0,210, 105)

// Right top bar button
//#define NavigationBarRightButton UIImage *imgOk = [UIImage imageNamed:@"btnok.png"];UIButton *btnOk = [UIButton buttonWithType:UIButtonTypeCustom];[btnOk setBackgroundColor:[UIColor clearColor]];btnOk.bounds = CGRectMake( 0, 0, imgOk.size.width, imgOk.size.height );btnOk.frame = CGRectMake(0, 0, 40, imgOk.size.height );[btnOk setImage:imgOk forState:UIControlStateNormal];[btnOk setContentHorizontalAlignment:UIControlContentHorizontalAlignmentRight ];[btnOk addTarget:self action:@selector(saveAction:) forControlEvents:UIControlEventTouchUpInside];UIBarButtonItem *barBtn_save = [[UIBarButtonItem alloc] initWithCustomView:btnOk];self.navigationItem.rightBarButtonItem = barBtn_save
#define NavigationBarRightButton     UIBarButtonItem *barBtn_save=[[UIBarButtonItem alloc]initWithTitle:@"Save" style:UIBarButtonItemStylePlain target:self action:@selector(saveAction:)];self.navigationItem.rightBarButtonItem = barBtn_save


#define kRole @"role"
#define kUserName @"username"
#define kProfile_pic @"profile_pic"
#define kPassword @"password"
#define kAge @"age"
#define kSex @"sex"
#define kAPI_Key @"api_key"
#define kUserDetail @"user_detail"
#define kData @"data"
#define kTokenData @"token_data"
#define kTokenValue @"tokenValue"
#define kToken @"token"
#define kUserId @"userid"
#define kTimeZone @"timezone"
#define kDiscription @"description"
#define kLocation @"location"
#define kCity @"city"
#define kResult @"result"
#define kDate @"date"
#define kToUser @"to_user"
#define kPagination @"pagination"
#define kImages @"images"
#define kPageCount @"page_count"
#define kPerPage @"perPage"
#define kCurrentdate @"current_time"
#define kDictKeyNSUser   @"dict_key"
#define kstateKey        @"stateKey"
#define kFB_userGender @"gender"
#define kNewYork @"NY"//New York
#define kCalifornia @"CA"//California
#define kAdminContact @"admin_contact"
#define kStatus @"status"
#define kErrorDisplayMessage @"errorDisplayMessage"
#define kErrorCode @"errorCode"
#define kErrorMessage @"errorMessage"
#define kStatusSuccess @"success"
#define kStatusFailed @"failed"
#define kFL2 @"FL2"
#define kAPS @"aps"
#define kNotificationId @"notification_id"
#define kNotificationSetting @"notify"
#define kBadgeCount @"badge"
#define kDeviceId @"device_id"

#define kAddress @"address"


#define kMessage @"message"



#define kFirstName           @"firstname"
#define kLastName            @"lastname"
#define kEmail               @"email"
#define KRelationship        @"relationship"
#define kphone1              @"phone"
#define kphone2              @"local_phone"
#define kemail               @"username"
#define kPassword            @"password"
#define kConfirmPassword     @"cPassword"
#define kHotelName           @"billing_name"
#define kCrossStreet         @"address_1"
#define kStreetAddress       @"streat_address"
#define kCity                @"city"
#define kState               @"state"
#define kStateList           @"stateList"
#define kCountryId           @"country_id"
#define kZip                 @"zipcode"
#define kLocalAddress        @"local_address"
#define kAlternateAddress    @"alternate_address"
#define kAddressType         @"address_type"
#define kAvailableCredits    @"available_credits"
#define kChildCount          @"child_count"
#define kStateId             @"state_id"
#define kAddressId           @"address_id"
#define kPassword            @"password"
#define kCurrentPassword     @"current_password"
#define kAutherizePaymentId  @"authorizenet_payment_profile_id"
#define klocal               @"local"
#define kJDetail             @"job_detail"
#define kTimeZone            @"timezone"
#define kProfileStatus       @"profile_completed"



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



#define kSpouseFirstName      @"spouse_firstname"
#define kSpouseRelation       @"spouse_relation"
#define kSpousePhone1         @"spouse_phone1"
#define kSpousePhone2         @"spouse_phone2"

#define kEmergencycontactName  @"emergency_contact"
#define kEmergencyRelation     @"emergency_relation"
#define kEmergencyPhone        @"emergency_phone"

// Jobs key
#define kJobDetail             @"jobDetails"
#define kJobList               @"jobList"
#define kJobStartDate          @"job_start_date"
#define kJobEndDate            @"job_end_date"
#define kPreferences           @"preferences"
#define kActualEndDate         @"actual_end_date"
#define kActualStartDate       @"actual_start_date"
#define kChildren              @"children"
#define kClientUserId          @"client_user_id"
#define kJobId                 @"job_id"
#define kJobStatus             @"job_status"
#define kJobPostedDate         @"jobs_posted_date"
#define kNotes                 @"notes"
#define kRate                  @"rate"
#define kSitterFirstName       @"sitter_firstname"
#define kSitterLastName        @"sitter_lastname"
#define kSitterPhone           @"sitter_phone"
#define kSitterUserId          @"sitter_user_id"
#define kSitterUserName        @"sitter_username"
#define kCompleatedDate        @"completed_date"
#define kLastModifiedDate      @"last_modified_date"
#define kTotalPaid             @"total_paid"
#define kTotalHours           @"total_hours" 
#define kTotalAssigned         @"total_assigned"
#define ksitterid              @"sitter_id"
#define ksitterThumbImage       @"sitter_thumb_image"

// Card keys
#define kCno         @"card_number"
#define kCmon        @"card_Month"
#define kCyear       @"card_Year"
#define kCvno        @"cardCVV"
#define kCardInfo    @"card_info"
#define kNameOnCard  @"name_on_card"
#define kSaveCard    @"save_card"
#define kPackageId   @"package_id"
#define kCardList    @"cardsList"
#define kCardDetail  @"card_detail"
#define kBookinFees  @"bookingFee"
#define kcreditsUsed @"creditsUsed"
#define kAvailableCredits @"available_credits"
#define kPackageData @"packageData"
#define kCredits     @"credits"
#define kPrice       @"price"
#define kOrdering    @"ordering"
#define kPackageName @"package_name"

// Sitter Keys
#define kProfileData        @"profileData"
#define kAboutMe            @"about_me"
#define kAgreedToTerms      @"agreed_to_terms"
#define kAvailableJobs      @"available_jobs"
#define kBabySitterTraining @"babysitter_training_date"
#define kCleanDriveRecord   @"clean_drive_record"
#define kCompleatedJobs     @"completed_jobs"
#define kConfirmedJobs      @"confirmed_jobs"
#define kCprAdult           @"cpr_adult"
#define kCprAdultDate       @"cpr_adult_date"
#define kCprDate            @"cpr_date"
#define kCprHolder          @"cpr_holder"
#define kCriminalRecord     @"criminal_record"
#define kCurrentCity        @"current_city"
#define kEarnings           @"earnings"
#define kEducation          @"education"
#define kExpSummary         @"exp_summary"
#define kFirstAidCart       @"first_aid_cert"
#define kFirstAidCertDate   @"first_aid_cert_date"
#define kHimptonBabySitterTraining @"hampton_babysitter_training"
#define kHaveCar            @"have_car"
#define kHaveChild          @"have_child"
#define kInfantTraining     @"infant_training"
#define kInfantTrainingDate @"infant_training_date"
#define kNumOfRating        @"num_of_rating"
#define kOtherPreference    @"otherpreference"
#define kPreference         @"preference"
#define kPreferences        @"preferences"
#define kPreferId           @"prefer_id"
#define kPreferName         @"prefer_name"
#define kRating             @"rating"
#define kTraits             @"traits"
#define kSitterId           @"userid"
#define kUserType           @"usertype"
#define kWaterCertDate      @"water_cert_date"
#define kWaterCertification @"water_certification"
#define kCertification      @"certification"
#define kArea               @"area"
#define kChildPreferences   @"child_preferences"
#define kOther              @"other"
#define kLanguage           @"language"
#define kIsSelected         @"is_selected"
#define kName               @"name"
#define kGroupName          @"group_name"
#define kJobPreferList      @"jobPreferList"
#define kPreferName         @"prefer_name"


#define DEVICE_TOKEN @"deviceToken"

#define TIMEOUT_INTERVAL 60.0i

#define kOFFSET_FOR_KEYBOARD 216.0


#define kAddSettingButtonForNavigation  UIImage *backImage = [UIImage imageNamed:@"setting-icon"];UIButton *btnSetting = [UIButton buttonWithType:UIButtonTypeCustom];btnSetting.bounds = CGRectMake( 0, 0, 20, 20 );btnSetting.frame = CGRectMake(0, 0, 20, 20 ); [btnSetting setImage:backImage forState:UIControlStateNormal];[btnSetting setContentHorizontalAlignment: isiOS7?UIControlContentHorizontalAlignmentLeft:UIControlContentHorizontalAlignmentCenter];[btnSetting addTarget:self action:@selector(onClickedMenu:) forControlEvents:UIControlEventTouchUpInside];UIBarButtonItem *btnMenu = [[UIBarButtonItem alloc] initWithCustomView:btnSetting]; self.navigationItem.rightBarButtonItem=btnMenu;

#define isiOS7 [[UIDevice currentDevice].systemVersion floatValue]>=7.0 ? YES : NO

#define kNavigationBarColor [UIColor colorWithRed:(255/255.0) green:(27/255.0) blue:(132/255.0) alpha:1]

#pragma mark trimed string
inline static NSString *trimedString(NSString *str){
    NSString *trimmedString = [str stringByTrimmingCharactersInSet:
                               [NSCharacterSet whitespaceAndNewlineCharacterSet]];
    return trimmedString;
}

