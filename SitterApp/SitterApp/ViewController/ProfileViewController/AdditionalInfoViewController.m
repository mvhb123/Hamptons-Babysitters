//
//  AdditionalInfoViewController.m
//  SitterApp
//
//  Created by Vikram gour on 08/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AdditionalInfoViewController.h"
#import "ApplicationManager.h"
#import "EditProfileViewController.h"

@interface AdditionalInfoViewController ()
{
 NSMutableArray      *arrayForSelectedSection;
}
@end

@implementation AdditionalInfoViewController
@synthesize dict_ArrayData,sitterInfo;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    //kAddDoneBarButtonForNavigation
    kAddSaveBarButtonForNavigation
    txtView_aboutMe.layer.borderWidth = 0.5;
    txtView_aboutMe.layer.borderColor = [UIColor grayColor].CGColor;
    self.array_specialNeeds=[[NSMutableArray alloc]init];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    self.sitterAdditionalInfo=[[SitterAdditionInformation alloc]init];
    self.sitterAdditionalInfo.array_CertAndTraining =self.sitterInfo.array_Certificates;
    self.sitterAdditionalInfo.array_Area =self.sitterInfo.array_Area;
    self.sitterAdditionalInfo.array_ChildPreferences =self.sitterInfo.array_Child_preferences;
    self.sitterAdditionalInfo.array_Language =self.sitterInfo.array_Language;
    self.sitterAdditionalInfo.array_OtherPreferences =self.sitterInfo.array_Other_preferences;
    [ApplicationManager getInstance].sitterAdditionInfo=self.sitterAdditionalInfo ;
    
    self.dictUserInfo=[[NSMutableDictionary alloc] init];
    dict_selectedInfo=[[NSMutableDictionary alloc]init];
    [imgUserProfile loadImageFromURL:self.sitterInfo.sitterProfileImageUrl];
   
    [txtView_aboutMe setText:trimedString(self.sitterInfo.sitterAboutMe)];
    [txt_firstName setText:self.sitterInfo.sitterFirstName];
    [txt_lastName setText:self.sitterInfo.sitterLastName];
    [txt_phone1 setText:self.sitterInfo.sitterPhone1];
    [txt_phone2 setText:self.sitterInfo.sitterPhone2];
    [txt_email setText:self.sitterInfo.sitterEmail];
    
    
    self.array_IndexTag=[[NSMutableArray alloc] init];
    self.array_IndexBtnTag=[[NSMutableArray alloc] init];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    dict_selectedInfo=[[NSMutableDictionary alloc]init];
    datePicker.maximumDate=[NSDate date];
    if (!arrayForSelectedSection) {
        arrayForSelectedSection    = [NSMutableArray arrayWithObjects:[NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO] , nil];
    }
    self.view.backgroundColor=kBackgroundColor;
    self.tbl_additionalInfo.separatorStyle=UITableViewCellSeparatorStyleNone;
    view_datePicker.frame=CGRectMake(0, self.view.frame.size.height+20, view_datePicker.frame.size.width, view_datePicker.frame.size.height);

    [self getAdditionInformation];
  
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(onTouchOnBackground:)];
    [view_mainBG addGestureRecognizer:tap];
    [self.backgroundScrollView setBackgroundColor:kBackgroundColor];
    [view_mainBG setBackgroundColor:kBackgroundColor];
    self.btnSpecialNeed=[UIButton buttonWithType:UIButtonTypeCustom];
     [self.btnSpecialNeed setTitleColor:kColorGrayDark forState:UIControlStateNormal];
     [self.btnSpecialNeed setTitleColor:kColorGrayDark forState:UIControlStateSelected];
    
     [self getSpecialNeed];
    //self.btnSpecialNeed.selected=YES;
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.navigationItem.title=@"Edit Profile";
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewDidLayoutSubviews{
    [super viewDidLayoutSubviews];
    [self setFontAndSize];
    self.backgroundScrollView=scrlViewMain;
    [scrlViewMain setContentSize:CGSizeMake(self.view.bounds.size.width,view_mainBG.frame.size.height+self.tbl_additionalInfo.frame.size.height)];
    }

#define mark -User Define method
-(void)getSpecialNeed{
    NSMutableDictionary *d=[[NSMutableDictionary alloc]init];
    [d setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kGetSpecialNeedListApi];
    networkManager.delegate = self;
    [networkManager specialNeedList:d forRequestNumber:3];
}
#pragma mark - NIDropdown
- (IBAction)onClickSpecialNeed:(id)sender {
    self.btnSpecialNeed.selected=!self.btnSpecialNeed.selected;
    [self.tbl_additionalInfo reloadData];
    [self showDropDown];
//    [self performSelector:@selector(showDropDown) withObject:btn afterDelay:0.2];
   
}
-(void)showDropDown{
   
    [self performSelector:@selector(reloadTbl) withObject:nil afterDelay:0.3];
}
-(void)reloadTbl{
   // UIButton *btn=(UIButton*)sender;
    
    NSMutableArray *array_specialNeed = [[NSMutableArray alloc]init];
    for (NSDictionary *d in self.array_specialNeed) {
        [array_specialNeed addObject:[d safeObjectForKey:@"special_need"]];
    }
    DDLogInfo(@"Selected ....... %d",self.btnSpecialNeed.selected);
    if(self.dropDownSpecialNeed == nil) {
        CGFloat f = 150;
        self.dropDownSpecialNeed = [[NIDropDown alloc]showDropDown:self.btnSpecialNeed :&f :array_specialNeed :nil :@"down"];
        self.dropDownSpecialNeed.delegate = self;
        self.dropDownSpecialNeed.tag=101;
        //        [self.tbl_additionalInfo bringSubviewToFront:self.dropDownSpecialNeed];
    }
    else {
        [self.dropDownSpecialNeed hideDropDown:self.btnSpecialNeed];
        [self dealloc_dropDown];
    }
   
}
-(void)dealloc_dropDown{
    self.dropDownSpecialNeed=nil;
    
}
#pragma mark-NIDropDownDelegate
- (void) niDropDownDelegateMethod: (NIDropDown *) sender {
    self.btnSpecialNeed.selected=NO;
        [self dealloc_dropDown];
    NSMutableDictionary *dtemp=[self.array_specialNeeds objectAtIndex:0];
    [dtemp setSafeObject:self.btnSpecialNeed.titleLabel.text forKey:@"specialNeed"];
    [dtemp setSafeObject:@"yes" forKey:@"is_selected"];
    [dict_selectedInfo setSafeObject:self.btnSpecialNeed.titleLabel.text forKey:kOtherPreferences];
    [self.array_specialNeeds replaceObjectAtIndex:0 withObject:dtemp];
    UIImage *img=[UIImage imageNamed:@"select"];
    //[self.btnSpecialNeed setImage:img forState:UIControlStateNormal];
   // [self.btnSpecialNeed setImage:img forState:UIControlStateSelected];
    
    self.btnSpecialNeed.imageEdgeInsets = UIEdgeInsetsMake(0., self.btnSpecialNeed.frame.size.width -img.size.width, 0., 0.);
    [self.tbl_additionalInfo reloadData];

}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [txtView_aboutMe resignFirstResponder];
    [self.view endEditing:YES];
}
-(void)setFontAndSize{
    [txtView_aboutMe setContentOffset:CGPointMake(0, 0)];
    self.backgroundScrollView.translatesAutoresizingMaskIntoConstraints  = YES;
    [btn_additionalInfo.titleLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize16]];
    [btn_additionalInfo setTitle:@"Additional Information" forState:UIControlStateNormal];
    [lbl_aboutMe setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_firstName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_lastName setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_phone1 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_phone2 setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_email setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    
    [txtView_aboutMe setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    self.sitterAdditionalInfo=[ApplicationManager getInstance].sitterAdditionInfo;
    //Set font color
    [lbl_firstName setTextColor:kColorGrayDark];
    [lbl_lastName setTextColor:kColorGrayDark];
    [lbl_email setTextColor:kColorGrayDark];
    [lbl_phone1 setTextColor:kColorGrayDark];
    [lbl_phone2 setTextColor:kColorGrayDark];
    [lbl_aboutMe setTextColor:kColorGrayDark];
    [txtView_aboutMe setTextColor:kColorGrayDark];
    [txt_firstName setTextColor:kColorGrayDark];
    [txt_lastName setTextColor:kColorGrayDark];
    [txt_phone1 setTextColor:kColorGrayDark];
    [txt_phone2 setTextColor:kColorGrayDark];
    [txt_email setTextColor:kColorGrayDark];
}

/**
 This method is called for generating unique id string
 @param none
 @returns NSString
 */
- (NSString *)generateUUID {
    CFUUIDRef theUUID = CFUUIDCreate(NULL);
    CFStringRef string = CFUUIDCreateString(NULL, theUUID);
    CFRelease(theUUID);
    return (__bridge  NSString *)string;
}
-(void)getAdditionInformation
{
    NSMutableDictionary *dict_apiData=[[NSMutableDictionary alloc] init];
    [dict_apiData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    if([[NSUserDefaults standardUserDefaults] objectForKey:DEVICE_TOKEN]){
        [dict_apiData setSafeObject:[[NSUserDefaults standardUserDefaults]objectForKey:DEVICE_TOKEN] forKey:DEVICE_TOKEN];
    }
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kGetAdditionalInfoList_API];
    networkManager.delegate = self;
    [networkManager getAllAdditionalInfo:dict_apiData forRequestNumber:1];
}
-(void)ShowDatePicker{
    [UIView beginAnimations:@"MoveUp" context:nil];
    [UIView setAnimationDuration:0.3];
    view_datePicker.frame=CGRectMake(0, self.view.frame.size.height-260, view_datePicker.frame.size.width, view_datePicker.frame.size.height);
    [UIView setAnimationDelegate:self];
    [UIView commitAnimations];

}
-(void)hideDatePicker{
    [UIView beginAnimations:@"MoveDown" context:nil];
    [UIView setAnimationDuration:0.3];
    view_datePicker.frame=CGRectMake(0, self.view.frame.size.height+20, view_datePicker.frame.size.width, view_datePicker.frame.size.height);
    [UIView setAnimationDelegate:self];
    [UIView commitAnimations];
}
/**
 This method is used for saving update profile data
 @param id A sender object which holds the UIButton object which initiated this action
 @returns nil
 */
-(void)onClickedSave:(id)sender{
    if ([self checkUserData] && [self checkData]) {
        [self.view endEditing:YES];
        [self.dictUserInfo setObject:txt_email.text forKey:kUserName];
        [self.dictUserInfo setObject:txt_firstName.text forKey:kFirstName];
        [self.dictUserInfo setObject:txt_lastName.text forKey:kLastName];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone1.text] forKey:kPhone];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone2.text] forKey:kLocal_Phone];
        [self.dictUserInfo setObject:trimedString(txtView_aboutMe.text) forKey:kAbout_Me];
        [self.dictUserInfo setObject:self.sitterInfo.sitterId forKey:kUserId];
      
        
        [dict_selectedInfo addEntriesFromDictionary:self.dictUserInfo];
        [dict_selectedInfo setSafeObject:[self generateUUID] forKey:@"profileImageName"];
        [dict_selectedInfo setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_selectedInfo setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
        
        if (dict_saveData.count>0)
        {
            [dict_selectedInfo addEntriesFromDictionary:dict_saveData];
        }
        else
        {
            [self onClickedDone:nil];

        }
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateProfile_API];
        networkManager.delegate = self;
        [networkManager updateUserProfile:dict_selectedInfo imageData:imageData forRequestNumber:2];
    }
}

/**
 This method is used for setting preferences for api calling
 @param NSString preId
 @returns NSString
 */
-(NSString*)setPreferencesForAPI:(NSString *)preId{
    NSString *str=@"";
    if ([dict_selectedInfo objectForKey:kPreferences]) {
        str=[dict_selectedInfo objectForKey:kPreferences];
    }
    
    str=[str stringByAppendingString:[NSString stringWithFormat:@",%@",preId]];
    return str;
}

/**
 This method is used for updating profile image or user
 @param id A sender object which holds the UIButton object which initiated this action
 @returns nil
 */

-(IBAction)onClickedUserImage:(id)sender{
    NSString *btn_camera = @"Open Camera";
    NSString *btn_album = @"Open Photo Album";
    NSString *btn_remove = @"Remove Photo";
    NSString *cancelTitle = @"Cancel";
    actionSheetForImage=[[UIActionSheet alloc]initWithTitle:nil delegate:self cancelButtonTitle:cancelTitle destructiveButtonTitle:nil otherButtonTitles:btn_camera,btn_album,btn_remove, nil];
    [actionSheetForImage showInView:self.view];
}
-(void)onClickedDone:(id)sender{
   // if ([self checkData]) {
    if ([dict_selectedInfo safeObjectForKey:kPreferences]) {
        [dict_selectedInfo removeObjectForKey:kPreferences];
    }
       for (NSDictionary *d in self.array_Area) {
        if ([[[d safeObjectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d objectForKey:@"prefer_id"]] forKey:@"preferences"];
        }
    }
    for (NSDictionary *d in self.array_Language) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d objectForKey:@"prefer_id"]] forKey:@"preferences"];
        }
    }
    for (NSDictionary *d in self.array_OtherPreferences) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d objectForKey:@"prefer_id"]] forKey:@"preferences"];
        }
    }
    for (NSDictionary *d in self.array_ChildPreferences) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d objectForKey:@"prefer_id"]] forKey:@"preferences"];
        }
    }
    for (NSDictionary *d in self.array_CertAndTraining) {
        if ([[[d safeObjectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]])
        {
            [dict_selectedInfo setSafeObject:[d objectForKey:kIsSelected] forKey:[d objectForKey:@"key1"]];
            if (![[d safeObjectForKey:@"date"] isEqualToString:@"NA"]) {
                [dict_selectedInfo setSafeObject:[d safeObjectForKey:@"date"] forKey:[d safeObjectForKey:@"key2"]];
            }else{
                [dict_selectedInfo setSafeObject:[d safeObjectForKey:kIsSelected] forKey:[d safeObjectForKey:@"key1"]];
            }
        }
        else{
                [dict_selectedInfo setSafeObject:@"No" forKey:[d safeObjectForKey:@"key1"]];
            }
    }
  
   //Remove , from start of the stinrg
    NSString *strPref=@"";
    if ([dict_selectedInfo objectForKey:@"preferences"]) {
        strPref=[dict_selectedInfo objectForKey:@"preferences"];
        strPref=[strPref substringFromIndex:1];
    }
    [dict_selectedInfo setSafeObject:strPref forKey:kPreferences];
        
    NSMutableArray *arrTemp=[[NSMutableArray alloc]init];
    for (NSDictionary *d in self.array_Area) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"]){
            [arrTemp addObject:d];
        }
    }
    self.sitterAdditionalInfo.array_Area=[arrTemp mutableCopy];
    [arrTemp removeAllObjects];
    for (NSDictionary *d in self.array_CertAndTraining) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"]){
            [arrTemp addObject:d];
        }
    }
    self.sitterAdditionalInfo.array_CertAndTraining=[arrTemp mutableCopy];
    [arrTemp removeAllObjects];
    for (NSDictionary *d in self.array_ChildPreferences) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"]){
            [arrTemp addObject:d];
        }
    }
    self.sitterAdditionalInfo.array_ChildPreferences=[arrTemp mutableCopy];
    [arrTemp removeAllObjects];
    for (NSDictionary *d in self.array_Language) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"]){
            [arrTemp addObject:d];
        }
    }
    self.sitterAdditionalInfo.array_Language=[arrTemp mutableCopy];
    [arrTemp removeAllObjects];
    for (NSDictionary *d in self.array_OtherPreferences) {
        if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"]){
            [arrTemp addObject:d];
        }
    }
    self.sitterAdditionalInfo.array_OtherPreferences=[arrTemp mutableCopy];
    //[ApplicationManager getInstance].sitterAdditionInfo=self.sitterAdditionalInfo;
//    [[NSNotificationCenter defaultCenter] postNotificationName:kNotificationSelectedAdditionaInfo object:nil userInfo:dict_selectedInfo];
//    [self.navigationController popViewControllerAnimated:YES];
//}
}

-(IBAction)onValueChangeSwCertificate:(UISwitch *)sender{
    NSInteger idx=sender.tag;
    if (idx!=1001) {
        [self.array_IndexTag addObject:[NSString stringWithFormat:@"%ld",(long)idx]];
        NSMutableDictionary *dictTemp=[[self.array_CertAndTraining objectAtIndex:(int)idx] mutableCopy];
        if (sender.isOn)
        {
            [dictTemp setSafeObject:@"Yes" forKey:@"is_selected"];
        }
        else
        {
            [dictTemp setSafeObject:@"No" forKey:@"is_selected"];
        }
        [self.array_CertAndTraining replaceObjectAtIndex:idx withObject:dictTemp];
        NSIndexPath* rowToReload = [NSIndexPath indexPathForRow:idx inSection:0];
        NSArray* rowsToReload = [NSArray arrayWithObjects:rowToReload, nil];
        [self.tbl_additionalInfo reloadRowsAtIndexPaths:rowsToReload withRowAnimation:UITableViewRowAnimationFade];
    }else{
        DDLogInfo(@"other yes");
        NSMutableDictionary *dictTemp=[[self.array_specialNeeds objectAtIndex:0] mutableCopy];
        if (sender.isOn)
        {
            [dictTemp setSafeObject:@"Yes" forKey:@"is_selected"];
        }
        else
        {
            [dictTemp setSafeObject:@"No" forKey:@"is_selected"];
            [self.btnSpecialNeed setTitle:@"" forState:UIControlStateNormal];
            [self.btnSpecialNeed setTitle:@"" forState:UIControlStateSelected];
            [dict_selectedInfo setSafeObject:@"" forKey:kOtherPreferences];

        }
        [self.btnSpecialNeed setSelected:NO];
        [self.array_specialNeeds replaceObjectAtIndex:0 withObject:dictTemp];
        NSIndexPath* rowToReload = [NSIndexPath indexPathForRow:self.array_CertAndTraining.count inSection:0];
        NSArray* rowsToReload = [NSArray arrayWithObjects:rowToReload, nil];
        [self.tbl_additionalInfo reloadRowsAtIndexPaths:rowsToReload withRowAnimation:UITableViewRowAnimationFade];
    }
    
}

-(IBAction)onClickedCertificateCalender:(UIButton*)sender{
    [self.array_IndexBtnTag addObject:[NSString stringWithFormat:@"%ld",(long)sender.tag]];
    datePicker.tag=sender.tag;
    [self ShowDatePicker];
}


- (IBAction)onClickedDateDonebutton:(id)sender {
    NSMutableDictionary *dictTemp=[[self.array_CertAndTraining objectAtIndex:datePicker.tag] mutableCopy];
    [dictTemp setObject:[self getDateForAPI:datePicker.date] forKey:@"date"];
    [self.array_CertAndTraining replaceObjectAtIndex:datePicker.tag withObject:dictTemp];
    NSIndexPath* rowToReload = [NSIndexPath indexPathForRow:datePicker.tag inSection:0];
    NSArray* rowsToReload = [NSArray arrayWithObjects:rowToReload, nil];
    [self.tbl_additionalInfo reloadRowsAtIndexPaths:rowsToReload withRowAnimation:UITableViewRowAnimationFade];
    [self hideDatePicker];
}

- (IBAction)onClickedDateCancelbutton:(id)sender {
    [self hideDatePicker];

}
-(NSString*)getDateForAPI:(NSDate*)dt{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    // Convert date object into desired format
    [dateFormatter setDateFormat:@"MMM dd yyyy"];
    NSString *newDateString = [dateFormatter stringFromDate:dt];
    return newDateString;
}
-(void)setCheckMarkForSection:(NSIndexPath*)idx arr:(NSMutableArray*)arr{
    NSMutableDictionary *dictTemp=[[arr objectAtIndex:idx.row] mutableCopy];
    if ([[dictTemp objectForKey:@"is_selected"] isEqualToString:@"Yes"]) {
        [dictTemp setObject:@"No" forKey:@"is_selected"];
    }
    else
    {
        [dictTemp setObject:@"Yes" forKey:@"is_selected"];
    }
    [arr replaceObjectAtIndex:idx.row withObject:dictTemp];
    NSIndexPath* rowToReload = [NSIndexPath indexPathForRow:idx.row inSection:idx.section];
    NSArray* rowsToReload = [NSArray arrayWithObjects:rowToReload, nil];
    [self.tbl_additionalInfo reloadRowsAtIndexPaths:rowsToReload withRowAnimation:UITableViewRowAnimationFade];
}
-(BOOL)checkUserData{
    if([txtView_aboutMe.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterAboutMe];
        return NO;
    }else if([txt_firstName.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterName];
        return NO;
    }else if([txt_lastName.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterLastName];
        return NO;
    }else if([txt_phone1.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterPhone1];
        return NO;
    }
    else if ([txt_email.text length]>0 && ![txt_email.text isValidEmail])//[app isValidEmailId:txt_userName.text])
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterValidEmail];
        return NO;
    }
    return YES;

}

/**
 This method is used to check the validation for data
 @param none
 @returns BOOL
 */
-(BOOL)checkData{
    BOOL isValid=YES;
    for (NSDictionary *d in self.array_CertAndTraining) {
        if ([[[d safeObjectForKey:kIsSelected] lowercaseString] isEqualToString:@"yes"] && ![[d safeObjectForKey:kDate] isEqualToString:@"NA"]) {
            if ([[d safeObjectForKey:kDate] isEqualToString:@""]) {
                 [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterCertificatesDate];
                isValid=NO;
                break;
            }
        }
    }
    return isValid;
}
#pragma mark - UIActionSheetDelegate
- (void)actionSheet:(UIActionSheet *)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex
{
    DDLogInfo(@"Image picker");
    if (buttonIndex==0) {
        if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera]) {
            [self showImagePicker:buttonIndex];
        }
        else{
            UIAlertView *alert=[[UIAlertView alloc] initWithTitle:@""  message:@"Camera is not available in this device." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
            [alert show];
        }
    }
    if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypePhotoLibrary] && buttonIndex==1) {
        [self showImagePicker:buttonIndex];
    }
    if (buttonIndex==2) {
        imgUserProfile.imageView.image=[UIImage imageNamed:@"defaultProfile.png"];
        
        [dict_selectedInfo setSafeObject:@"yes" forKey:@"delete_pic"];
    }
}
#pragma mark - UIImagePickerControllerDelegate

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
{
    UIImage *image = [info objectForKey:@"UIImagePickerControllerEditedImage"];
    image = [self imageWithImage:image scaledToSizeKeepingAspect:CGSizeMake(image.size.width,image.size.height)];
    imgUserProfile.imageView.image=image;
    imageData = UIImageJPEGRepresentation(image ,1.0);
    [dict_selectedInfo setSafeObject:@"no" forKey:@"delete_pic"];
    [self dismissViewControllerAnimated:YES completion:^{
        
    }];
}

-(void)showImagePicker:(NSInteger)pickerType{
    UIImagePickerController *imgPicker=[[UIImagePickerController alloc]init];
    imgPicker.delegate=self;
    imgPicker.allowsEditing=YES;
    if (pickerType==0) {
        imgPicker.sourceType=UIImagePickerControllerSourceTypeCamera;
    }else if (pickerType==1) {
        imgPicker.sourceType=UIImagePickerControllerSourceTypePhotoLibrary;
    }
    [self presentViewController:imgPicker animated:YES completion:^{
        
    }];
}
/**
 This methos is used for adjusting image size
 @param id image,newSize
 @return UIImage object
 */
- (UIImage*)imageWithImage:(UIImage*)image scaledToSizeKeepingAspect:(CGSize)newSize
{
    // Create a graphics image context
    //    if (image.size.width<302 && image.size.height<194) {
    if (image.size.width<301 && image.size.height<461)
    {
        return image;
    }
    float imgHeight = image.size.height;
    float imgWeight = image.size.width;
    float fact = 0.0,fact1,fact2;
    fact1 = imgHeight/newSize.height;
    fact2 = imgWeight/newSize.width;
    if (fact1>fact2)
    {
        fact=fact1;
    }
    else
        fact=fact2;
    imgHeight = imgHeight / fact;
    imgWeight = imgWeight / fact;
    newSize  = CGSizeMake(imgWeight, imgHeight);
    UIGraphicsBeginImageContext(newSize);
    
    // Tell the old image to draw in this new context, with the desired
    // new size
    [image drawInRect:CGRectMake(0,0,newSize.width,newSize.height)];
    
    // Get the new image from the context
    UIImage* newImage = UIGraphicsGetImageFromCurrentImageContext();
    
    // End the context
    UIGraphicsEndImageContext();
    
    // Return the new image.
    return newImage;
}


#pragma mark- UITextFieldDelegate
- (void)textFieldDidBeginEditing:(UITextField *)textField
{
    if(textField.tag==1001){
        [self.backgroundScrollView setContentOffset:CGPointMake(self.backgroundScrollView.contentOffset.x, self.backgroundScrollView.contentOffset.y+216) animated:YES];
    }
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    if (textField == txt_firstName) {
        [txt_lastName becomeFirstResponder];
    }else if (textField == txt_lastName) {
        [txt_phone1 becomeFirstResponder];
    }else if (textField == txt_phone1) {
        [txt_phone2 becomeFirstResponder];
    }else if (textField == txt_phone2) {
        [txt_email becomeFirstResponder];
    }else if(textField.tag==1001){
        [self.tbl_additionalInfo scrollToRowAtIndexPath:[NSIndexPath indexPathForItem:0 inSection:0] atScrollPosition:UITableViewScrollPositionTop animated:YES];
        [textField resignFirstResponder];
    }
    else
        [textField resignFirstResponder];
    return true;
}
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string{
    if (textField==txt_phone1 || textField==txt_phone2) {
        textField.text=[self.numFormatter formatText:textField.text];
    }else if (textField.tag==1001){
        NSString *strOther=[textField.text stringByReplacingCharactersInRange:range withString:string];
        // DDLogInfo(@"text %@    str %@",textField.text,strOther);
        [dict_selectedInfo setSafeObject:strOther forKey:kOtherPreferences];
        NSMutableDictionary *d=[[self.array_specialNeeds objectAtIndex:0] mutableCopy];
        [d setSafeObject:strOther forKey:@"specialNeed"];
        [d setSafeObject:@"yes" forKey:@"is_selected"];
        [self.array_specialNeeds replaceObjectAtIndex:0 withObject:d];
    }
    
    return YES;
}
#pragma mark - UITextViewDelegate

- (void)textViewDidBeginEditing:(UITextView *)textView
{
    
}

- (BOOL)textView:(UITextView *)textView shouldChangeTextInRange:(NSRange)range replacementText:(NSString *)text {
    
    return YES;
}

#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (alertView.tag==100) {
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];
    
}
#pragma mark UITableViewDatasource
- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView{
    return 5;
}
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (indexPath.section==0) {
        if (indexPath.row==self.array_CertAndTraining.count){
            if (self.btnSpecialNeed.selected) {
                return 230;
            }
            return 70;
        }else{
            return 35;
        }
    }
    return 25;
}

- (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
{
    if ([[arrayForSelectedSection objectAtIndex:section] boolValue]){
    if (section==0) {
            return [self.array_CertAndTraining count]+1;
    }
    if (section==1) {
        return [self.array_Area count];
    }
    if (section==2) {
        return [self.array_Language count];
    }
    if (section==3) {
        return [self.array_OtherPreferences count];
    }
    if (section==4) {
        return [self.array_ChildPreferences count];
    }
    }
    return 0;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = @"certAndTrainingCell";
    static NSString *otherCellIdentifier = @"otherCell";
    
    UpdateCertAndTrainingCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
    UITableViewCell *otherCell=[tableView dequeueReusableCellWithIdentifier:otherCellIdentifier];
    if (indexPath.section==0) {
        if (cell == nil)
        {
            NSArray *nibArray=[[NSBundle mainBundle] loadNibNamed:@"UpdateCertAndTrainingCell" owner:self options:nil];
            cell = [nibArray safeObjectAtIndex:0];
            [cell setBackgroundColor:[UIColor clearColor]];
            cell.accessoryType=UITableViewCellAccessoryNone;
            [cell.lbl_certType setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [cell.lbl_certDate setFont:[UIFont fontWithName:Roboto_Regular size:10.0]];
            [cell.lbl_certType setTextColor:kColorGrayDark];
            [cell.lbl_certDate setTextColor:kColorGrayDark];
            cell.selectionStyle=UITableViewCellSelectionStyleNone;

        }
        cell.btn_calender.tag=indexPath.row;
        if (indexPath.row==self.array_CertAndTraining.count) {
            [cell.lbl_certType setText:kOtherCert];
            [cell.lbl_certType sizeToFit];
            [cell.sw_certType setHidden:NO];
            [cell.btn_calender setHidden:YES];
            [cell.lbl_certDate setHidden:YES];
            [cell.sw_certType setTag:1001];
            float xpos=cell.lbl_certType.frame.origin.x;
            CGRect txtFrm=CGRectMake(xpos,35, self.tbl_additionalInfo.frame.size.width-(xpos+5), 30);
            [self.btnSpecialNeed setFrame:txtFrm];
            self.btnSpecialNeed.layer.borderWidth = 0.5;
            self.btnSpecialNeed.layer.borderColor = [UIColor grayColor].CGColor;
             UIImage *img=[UIImage imageNamed:@"select"];

            self.btnSpecialNeed.imageEdgeInsets = UIEdgeInsetsMake(0., self.btnSpecialNeed.frame.size.width -img.size.width, 0., 0.);
            //[self.btnSpecialNeed setImageEdgeInsets:UIEdgeInsetsMake(0, self.btnSpecialNeed.frame.size.width, 0,0)];
            [self.btnSpecialNeed.titleLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [self.btnSpecialNeed setTag:1001];
            [self.btnSpecialNeed addTarget:self action:@selector(onClickSpecialNeed:) forControlEvents:UIControlEventTouchUpInside];
           
            NSDictionary *dtemp=[self.array_specialNeeds objectAtIndex:0];
            [cell.contentView addSubview:self.btnSpecialNeed];
            UIImageView *imgView=[[UIImageView alloc]initWithFrame:CGRectMake(txtFrm.size.width-(img.size.width-4),txtFrm.origin.y, img.size.width,img.size.height)];
            [imgView setImage:img];
            [cell addSubview:imgView];
            if ([[[dtemp objectForKey:@"is_selected"] lowercaseString] isEqualToString:@"yes"]) {
                [self.btnSpecialNeed setTitle:[dtemp safeObjectForKey:@"specialNeed"] forState:UIControlStateNormal];
                [cell.sw_certType setOn:YES];
                [self.btnSpecialNeed setHidden:NO];
                [imgView setHidden:NO];
            }else{
                [cell.sw_certType setOn:NO];
                [self.btnSpecialNeed setHidden:YES];
                [imgView setHidden:YES];
            }
        }else{
        NSDictionary *d=[self.array_CertAndTraining objectAtIndex:indexPath.row];
        [cell.lbl_certType setText:[d valueForKey:@"name"]];
        [cell.sw_certType setTag:indexPath.row];
        [cell.btn_calender setTag:indexPath.row];
        
        if ([[[d objectForKey:@"is_selected"] lowercaseString] isEqualToString:@"yes"]) {
            [cell.sw_certType setOn:YES];
            [cell.lbl_certDate setHidden:NO];
            [cell.btn_calender setHidden:NO];
            [cell.lbl_certDate setText:[d objectForKey:@"date"]];
        }else{
            [cell.sw_certType setOn:NO];
            [cell.lbl_certDate setHidden:YES];
            [cell.btn_calender setHidden:YES];
            
            [cell.lbl_certDate setText:@""];
        }
        
        if ([[d objectForKey:@"date"] isEqualToString:@"NA"]) {
            [cell.lbl_certDate setHidden:YES];
            [cell.btn_calender setHidden:YES];
            [cell.lbl_certDate setText:@""];
        }
        }
        return cell;
    }else if (indexPath.section==1) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *d=[self.array_Area objectAtIndex:indexPath.row];
        otherCell.textLabel.text=[d objectForKey:@"prefer_name"];
        if ([[[d objectForKey:@"is_selected"] lowercaseString] isEqualToString:@"yes"]) {
            otherCell.accessoryType=UITableViewCellAccessoryCheckmark;
        }else{
            otherCell.accessoryType=UITableViewCellAccessoryNone;
        }
        return otherCell;
    }else if (indexPath.section==2) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *d=[self.array_Language objectAtIndex:indexPath.row];
        otherCell.textLabel.text=[d objectForKey:@"prefer_name"];
        if ([[d objectForKey:@"is_selected"] isEqualToString:@"Yes"]) {
            otherCell.accessoryType=UITableViewCellAccessoryCheckmark;
        }else{
            otherCell.accessoryType=UITableViewCellAccessoryNone;
        }
        return otherCell;
    }else if (indexPath.section==3) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *d=[self.array_OtherPreferences objectAtIndex:indexPath.row];
        otherCell.textLabel.text=[d objectForKey:@"prefer_name"];
        if ([[[d objectForKey:@"is_selected"] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            otherCell.accessoryType=UITableViewCellAccessoryCheckmark;
        }else{
            otherCell.accessoryType=UITableViewCellAccessoryNone;
        }
        return otherCell;
    }else if (indexPath.section==4) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *d=[self.array_ChildPreferences objectAtIndex:indexPath.row];
        otherCell.textLabel.text=[d objectForKey:@"prefer_name"];
        if ([[[d objectForKey:@"is_selected"] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
            otherCell.accessoryType=UITableViewCellAccessoryCheckmark;
        }else{
            otherCell.accessoryType=UITableViewCellAccessoryNone;
        }
        return otherCell;
    }
    return nil;
    
}
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    index=indexPath;
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    if (indexPath.section==1) {
       [self setCheckMarkForSection:indexPath arr:self.array_Area];
    }
    if (indexPath.section==2) {
        [self setCheckMarkForSection:indexPath arr:self.array_Language];
    }
    if (indexPath.section==3) {
        [self setCheckMarkForSection:indexPath arr:self.array_OtherPreferences];
    }
    if (indexPath.section==4) {
        [self setCheckMarkForSection:indexPath arr:self.array_ChildPreferences];
    }
}
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    UIView *viewHeader=[[UIView alloc]initWithFrame:CGRectMake(0, 0,self.tbl_additionalInfo.frame.size.width , 40)];
    viewHeader.tag                  = section;
    [viewHeader setBackgroundColor:[UIColor clearColor]];
    UIImageView *imgHeaderBg=[[UIImageView alloc]initWithImage:[UIImage imageNamed:@"accordian.png"]];
    [imgHeaderBg setFrame:viewHeader.frame];
    [viewHeader addSubview:imgHeaderBg];
    CGRect lblFrm=viewHeader.frame;
    lblFrm.origin.x=20;
    UILabel *lblHeaderTitle=[[UILabel alloc]initWithFrame:lblFrm];
    NSString *strTitle=@"";
    if (section==0) {
        strTitle= @"Skills & Certifications";
    }else if (section==1) {
        strTitle =@"Areas";
    }else if (section==2) {
        strTitle= @"Languages";
    }else if (section==3) {
        strTitle= @"Other Information";
    }else{
        strTitle= @"Child Preferences";
    }
    [lblHeaderTitle setFont:[UIFont fontWithName:Roboto_Regular size:FontSize16]];
    [lblHeaderTitle setTextColor:kColorWhite];
    [lblHeaderTitle setText:strTitle];
    [viewHeader addSubview:lblHeaderTitle];
    
    UITapGestureRecognizer  *headerTapped   = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(sectionHeaderTapped:)];
    [viewHeader addGestureRecognizer:headerTapped];
    
    
    BOOL manyCells                  = [[arrayForSelectedSection objectAtIndex:section] boolValue];
    //up or down arrow depending on the bool
    UIImageView *upDownArrow        = [[UIImageView alloc] initWithImage:manyCells ? [UIImage imageNamed:@"accarwDown"]:[UIImage imageNamed:@"accarw"]];
    upDownArrow.autoresizingMask    = UIViewAutoresizingFlexibleLeftMargin;
    upDownArrow.frame               = CGRectMake(viewHeader.frame.size.width-30,12.5, 15, 15);
    [viewHeader addSubview:upDownArrow];
    //[viewHeader sendSubviewToBack:self.dropDownSpecialNeed];
    return viewHeader;
}
- (UIView *)tableView:(UITableView *)tableView viewForFooterInSection:(NSInteger)section{
    UIView *footer  = [[UIView alloc] initWithFrame:CGRectZero];
    return footer;
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 40;
}
- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0;
}

#pragma mark - gesture tapped
- (void)sectionHeaderTapped:(UITapGestureRecognizer *)gestureRecognizer{
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:0 inSection:gestureRecognizer.view.tag];
    if (indexPath.row == 0) {
        BOOL collapsed  = [[arrayForSelectedSection objectAtIndex:indexPath.section] boolValue];
        collapsed       = !collapsed;
        [arrayForSelectedSection replaceObjectAtIndex:indexPath.section withObject:[NSNumber numberWithBool:collapsed]];
        
        //reload specific section animated
        NSRange range   = NSMakeRange(indexPath.section, 1);
        NSIndexSet *sectionToReload = [NSIndexSet indexSetWithIndexesInRange:range];
        [self.tbl_additionalInfo reloadSections:sectionToReload withRowAnimation:UITableViewRowAnimationNone];
    }
}


-(void)dataForView
{
     for (NSDictionary *d in self.sitterAdditionalInfo.array_CertAndTraining) {
        if ([[[d objectForKey:@"is_selected"] lowercaseString] isEqualToString:@"yes"]) {
            for (int i =0; i<self.array_CertAndTraining.count;++i) {
                NSDictionary *dictCert = [self.array_CertAndTraining objectAtIndex:i];
                if ([[dictCert objectForKey:@"key1"] isEqualToString:[d objectForKey:@"key1"]]) {
                    NSMutableDictionary *tempMutableDict = [[NSMutableDictionary alloc] initWithDictionary:dictCert];
                    [tempMutableDict setObject:@"Yes" forKey:@"is_selected"];
                    [tempMutableDict setObject:[d objectForKey:@"date"] forKey:@"date"];
                   [self.array_CertAndTraining replaceObjectAtIndex:i withObject:tempMutableDict];
                }
            }
        }
    }
    for (NSDictionary *d in self.sitterAdditionalInfo.array_Area) {
        for (int i =0; i<self.array_Area.count;++i) {
            NSDictionary *dictCert = [self.array_Area objectAtIndex:i];
            if ([[dictCert objectForKey:@"prefer_id"] isEqualToString:[d objectForKey:@"prefer_id"]]) {
                NSMutableDictionary *tempMutableDict = [[NSMutableDictionary alloc] initWithDictionary:dictCert];
                [tempMutableDict setObject:@"Yes" forKey:@"is_selected"];
             [self.array_Area replaceObjectAtIndex:i withObject:tempMutableDict];

            }
        }
    }
    for (NSDictionary *d in self.sitterAdditionalInfo.array_ChildPreferences) {
        for (int i =0; i<self.array_ChildPreferences.count;++i) {
            NSDictionary *dictCert = [self.array_ChildPreferences objectAtIndex:i];
            if ([[dictCert objectForKey:@"prefer_id"] isEqualToString:[d objectForKey:@"prefer_id"]]) {
                NSMutableDictionary *tempMutableDict = [[NSMutableDictionary alloc] initWithDictionary:dictCert];
                [tempMutableDict setObject:@"Yes" forKey:@"is_selected"];
              [self.array_ChildPreferences replaceObjectAtIndex:i withObject:tempMutableDict];

            }
        }
    }
    for (NSDictionary *d in self.sitterAdditionalInfo.array_Language) {
        for (int i =0; i<self.array_Language.count;++i) {
            NSDictionary *dictCert = [self.array_Language objectAtIndex:i];
            if ([[dictCert objectForKey:@"prefer_id"] isEqualToString:[d objectForKey:@"prefer_id"]]) {
                NSMutableDictionary *tempMutableDict = [[NSMutableDictionary alloc] initWithDictionary:dictCert];
                [tempMutableDict setObject:@"Yes" forKey:@"is_selected"];
             [self.array_Language replaceObjectAtIndex:i withObject:tempMutableDict];
            }
        }
    }
    for (NSDictionary *d in self.sitterAdditionalInfo.array_OtherPreferences) {
        for (int i =0; i<self.array_OtherPreferences.count;++i) {
            NSDictionary *dictCert = [self.array_OtherPreferences objectAtIndex:i];
            if ([[dictCert objectForKey:@"prefer_id"] isEqualToString:[d objectForKey:@"prefer_id"]]) {
                NSMutableDictionary *tempMutableDict = [[NSMutableDictionary alloc] initWithDictionary:dictCert];
                [tempMutableDict setObject:@"Yes" forKey:@"is_selected"];
                [self.array_OtherPreferences replaceObjectAtIndex:i withObject:tempMutableDict];

            }
        }
}
    [self.tbl_additionalInfo reloadData];
   
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                DDLogInfo(@"user data %@",dict_responseObj);
                //[[ApplicationManager getInstance] saveAdditionalInformation:dict_responseObj];
                self.array_CertAndTraining=[[[dict_responseObj objectForKey:kData]  objectForKey:@"certifications"] mutableCopy];
                self.array_Area=[[[[dict_responseObj objectForKey:kData]  objectForKey:@"sitterPreferList"] objectForKey:@"area"] mutableCopy];
                self.array_ChildPreferences=[[[[dict_responseObj objectForKey:kData]  objectForKey:@"sitterPreferList"] objectForKey:@"child_preferences"] mutableCopy];
                self.array_Language=[[[[dict_responseObj objectForKey:kData]  objectForKey:@"sitterPreferList"] objectForKey:@"language"] mutableCopy];
                self.array_OtherPreferences=[[[[dict_responseObj objectForKey:kData]  objectForKey:@"sitterPreferList"] objectForKey:@"other"] mutableCopy];
                [self.array_specialNeeds removeAllObjects];
                if ([self.sitterInfo.str_OtherPreferences isEqualToString:@""]) {
                    NSMutableDictionary *d=[[NSMutableDictionary alloc] init];
                    [d setSafeObject:@"" forKey:@"specialNeed"];
                    [d setSafeObject:@"No" forKey:@"is_selected"];
                    [self.array_specialNeeds addObject:d];
                }else{
                    NSMutableDictionary *d=[[NSMutableDictionary alloc] init];
                    [d setSafeObject:self.sitterInfo.str_OtherPreferences forKey:@"specialNeed"];
                    [d setSafeObject:@"yes" forKey:@"is_selected"];
                    [dict_selectedInfo setSafeObject:self.sitterInfo.str_OtherPreferences forKey:kOtherPreferences];
                    [self.array_specialNeeds addObject:d];
                }
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
             [self dataForView];
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                DDLogInfo(@"user data %@",dict_responseObj);
                [[ApplicationManager getInstance] saveLogInData:dict_responseObj];
                [[NSUserDefaults standardUserDefaults]setObject:dict_responseObj forKey:kLogedinUserDetail];
                [[NSUserDefaults standardUserDefaults]synchronize];
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj valueForKey:kMessage] delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
                [alert setTag:100];
                [alert show];
               
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        case 3:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                self.array_specialNeed=[[NSArray alloc] initWithArray:[dict_responseObj safeObjectForKey:kData]];
                
            }
            break;
        default:
            break;
    }
    
   
    // [self.tbl_additionalInfo reloadData];
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    // NSError *errorcode=(NSError *)error;
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark -- AlertView delegate
//- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
//{
//    if (alertView.tag==100) {
//         [self.navigationController popViewControllerAnimated:YES];
//    }
//   
//    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];
//
//
//}
-(void)dealloc{
    
}

/*
 #pragma mark - Navigation
 
 // In a storyboard-based application, you will often want to do a little preparation before navigation
 - (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
 // Get the new view controller using [segue destinationViewController].
 // Pass the selected object to the new view controller.
 }
 */

@end
