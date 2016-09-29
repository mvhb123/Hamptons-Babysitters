//
//  EditProfileViewController.m
//  SitterApp
//
//  Created by Vikram gour on 04/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "EditProfileViewController.h"
#import "AdditionalInfoViewController.h"
@interface EditProfileViewController ()

@end

@implementation EditProfileViewController
@synthesize sitterInfo;
- (void)viewDidLoad {
    [super viewDidLoad];
    kAddSaveBarButtonForNavigation
    // Do any additional setup after loading the view from its nib.
    [self.backgroundScrollView setHidden:YES];

    [self.backgroundScrollView setBackgroundColor:kBackgroundColor];
    [view_mainBG setBackgroundColor:kBackgroundColor];
    txtView_aboutMe.layer.borderWidth = 0.5;
    txtView_aboutMe.layer.borderColor = [UIColor grayColor].CGColor;
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
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(viewDidLayoutSubviews)
                                                 name:@"viewDidLayoutSubviews"
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(setSelectedAdditionalInfo:) name:kNotificationSelectedAdditionaInfo object:nil];
    
    [imgUserProfile loadImageFromURL:self.sitterInfo.sitterProfileImageUrl];
    //[imgUserProfile.layer setCornerRadius:50];
   // [imgUserProfile setClipsToBounds:YES];
    [txtView_aboutMe setText:trimedString(self.sitterInfo.sitterAboutMe)];
    [txt_firstName setText:self.sitterInfo.sitterFirstName];
    [txt_lastName setText:self.sitterInfo.sitterLastName];
    [txt_phone1 setText:self.sitterInfo.sitterPhone1];
    [txt_phone2 setText:self.sitterInfo.sitterPhone2];
    [txt_email setText:self.sitterInfo.sitterEmail];
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.navigationItem.title=@"Edit Profile";
}

-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
   
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
    [self.backgroundScrollView setHidden:NO];

}

-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    // Adjust frame for iPhone 4s
    if (self.view.bounds.size.height <= 568) {
        view_mainBG.frame = CGRectMake(0, 0, 320, 541); // 541 height is fixed for iPhone4 & iPhone5
    }else
    {
        view_mainBG.frame = CGRectMake(0, 0, self.view.bounds.size.width, self.view.bounds.size.height);
    }
    //DDLogInfo(@"size %@",NSStringFromCGSize(self.backgroundScrollView.contentSize));
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width, view_mainBG.frame.size.height)];
    
}

- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [txtView_aboutMe resignFirstResponder];
    [self.view endEditing:YES];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#define mark - UserDefineMethods

-(void)setSelectedAdditionalInfo:(NSNotification*)notofication{
    dict_saveData=notofication.userInfo;
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

/**
 This method is used for saving update profile data
 @param id A sender object which holds the UIButton object which initiated this action
 @returns nil
 */
-(void)onClickedSave:(id)sender{
    if ([self checkData]) {
        [self.view endEditing:YES];
        [self.dictUserInfo setObject:txt_email.text forKey:kUserName];
        [self.dictUserInfo setObject:txt_firstName.text forKey:kFirstName];
        [self.dictUserInfo setObject:txt_lastName.text forKey:kLastName];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone1.text] forKey:kPhone];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone2.text] forKey:kLocal_Phone];
        [self.dictUserInfo setObject:trimedString(txtView_aboutMe.text) forKey:kAbout_Me];
        [self.dictUserInfo setObject:self.sitterInfo.sitterId forKey:kUserId];
        if ([dict_selectedInfo objectForKey:kPreferences]) {
            [dict_selectedInfo removeObjectForKey:kPreferences];
        }
        for (NSDictionary *d in self.sitterInfo.array_Certificates) {
            if ([[[d objectForKey:kIsSelected] lowercaseString] isEqualToString:[@"Yes" lowercaseString]]) {
                [dict_selectedInfo setObject:[d objectForKey:kIsSelected] forKey:[d objectForKey:kKey1]];
                if (![[d objectForKey:kDate] isEqualToString:@"NA"]) {
                    [dict_selectedInfo setSafeObject:[d objectForKey:kDate] forKey:[d objectForKey:kKey2]];
                }else{
                    [dict_selectedInfo setSafeObject:[d objectForKey:kIsSelected] forKey:[d objectForKey:kKey1]];
                }
            }
            else{
                [dict_selectedInfo setObject:@"No" forKey:[d objectForKey:kKey1]];
            }
        }
           //Remove (,) from start of the string
        NSString *strPref=@"";
        if ([dict_selectedInfo objectForKey:kPreferences]) {
            strPref=[dict_selectedInfo objectForKey:kPreferences];
            strPref=[strPref substringFromIndex:1];
        }
        [dict_selectedInfo setSafeObject:strPref forKey:kPreferences];
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
            for (NSDictionary *d in self.sitterInfo.array_Area)
            {
                [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d safeObjectForKey:kPreferId]] forKey:kPreferences];
            }
            for (NSDictionary *d in self.sitterInfo.array_Language) {
                [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d safeObjectForKey:kPreferId]] forKey:kPreferences];
            }
            for (NSDictionary *d in self.sitterInfo.array_Other_preferences) {
                [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d safeObjectForKey:kPreferId]] forKey:kPreferences];
            }
            for (NSDictionary *d in self.sitterInfo.array_Child_preferences) {
                [dict_selectedInfo setSafeObject:[self setPreferencesForAPI:[d safeObjectForKey:kPreferId]] forKey:kPreferences];
            }
        }
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateProfile_API];
        networkManager.delegate = self;
        [networkManager updateUserProfile:dict_selectedInfo imageData:imageData forRequestNumber:1];
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

/**
 This method is used to switch the editional information page where user can add its additional information.
 @param id A sender object which holds the UIButton object which initiated this action
 @returns nil
 */
- (IBAction)onClicked_additionalInfo:(id)sender {
    if ([self checkData]) {
        [self.dictUserInfo setSafeObject:txt_email.text forKey:kUserName];
        [self.dictUserInfo setSafeObject:txt_firstName.text forKey:kFirstName];
        [self.dictUserInfo setSafeObject:txt_lastName.text forKey:kLastName];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone1.text] forKey:kPhone];
        [self.dictUserInfo setObject:[self.numFormatter rawText:txt_phone2.text] forKey:kLocal_Phone];
        [self.dictUserInfo setSafeObject:txtView_aboutMe.text forKey:kAbout_Me];
        [self.dictUserInfo setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
        AdditionalInfoViewController *viewAdditionalInfo=[[AdditionalInfoViewController alloc]initWithNibName:@"AdditionalInfoViewController" bundle:nil];
        viewAdditionalInfo.sitterAdditionalInfo=self.sitterAdditionalInfo ;
        self.navigationItem.title=@"";
        [self.navigationController pushViewController:viewAdditionalInfo animated:YES];
        }
}

/**
 This method is used to check the validation for data
 @param none
 @returns BOOL
 */
-(BOOL) checkData{
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

#pragma mark-APICalling
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
    [networkManager getAllAdditionalInfo:dict_apiData forRequestNumber:2];
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
    }else
        [textField resignFirstResponder];
    return true;
}
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string{
    if (textField==txt_phone1 || textField==txt_phone2) {
        textField.text=[self.numFormatter formatText:textField.text];
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
            
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                DDLogInfo(@"user data %@",dict_responseObj);
                [[ApplicationManager getInstance] saveAdditionalInformation:dict_responseObj];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
            
        default:
            break;
    }
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    // NSError *errorcode=(NSError *)error;
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}


@end
