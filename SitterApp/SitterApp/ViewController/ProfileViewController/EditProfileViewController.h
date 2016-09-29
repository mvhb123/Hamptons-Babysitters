//
//  EditProfileViewController.h
//  SitterApp
//
//  Created by Vikram gour on 04/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "SitterAdditionInformation.h"
@interface EditProfileViewController : AppBaseViewController<UIActionSheetDelegate,UIImagePickerControllerDelegate,UINavigationControllerDelegate>
{

    __weak IBOutlet UIView *view_mainBG;
    __weak IBOutlet AsyncImageView *imgUserProfile;
    __weak IBOutlet UITextView *txtView_aboutMe;
    __weak IBOutlet UITextField *txt_firstName;
    __weak IBOutlet UITextField *txt_lastName;
    __weak IBOutlet UITextField *txt_phone1;
    __weak IBOutlet UITextField *txt_phone2;
    __weak IBOutlet UITextField *txt_email;
    __weak IBOutlet UIButton *btn_additionalInfo;
    UIActionSheet *actionSheetForImage;
    NSMutableDictionary *dict_selectedInfo;
    NSDictionary *dict_Notification;
    NSDictionary *dict_saveData;
    NSData *imageData;
    BOOL  flag;
    __weak IBOutlet UILabel *lbl_aboutMe;
    __weak IBOutlet UILabel *lbl_firstName;
    __weak IBOutlet UILabel *lbl_lastName;
    __weak IBOutlet UILabel *lbl_phone1;
    __weak IBOutlet UILabel *lbl_phone2;
    __weak IBOutlet UILabel *lbl_email;
}
@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic,strong)SitterAdditionInformation *sitterAdditionalInfo;
@property(nonatomic,retain)NSMutableDictionary *dictUserInfo;
- (IBAction)onClicked_additionalInfo:(id)sender;
- (UIImage*)imageWithImage:(UIImage*)image scaledToSizeKeepingAspect:(CGSize)newSize;
-(NSString*)setPreferencesForAPI:(NSString *)preId;
@end
