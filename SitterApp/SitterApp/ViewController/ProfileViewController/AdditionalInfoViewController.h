//
//  AdditionalInfoViewController.h
//  SitterApp
//
//  Created by Vikram gour on 08/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "UpdateCertAndTrainingCell.h"
#import "SMUIScrollView.h"
@interface AdditionalInfoViewController : AppBaseViewController<UITextFieldDelegate,UIActionSheetDelegate,UIImagePickerControllerDelegate,UINavigationControllerDelegate,NIDropDownDelegate>
{
    UIPopoverController *calenderPopover;
    IBOutlet UIView *view_datePicker;
    __weak IBOutlet UIDatePicker *datePicker;
    NSMutableDictionary *dict_selectedInfo;
    NSMutableArray *array_LocalCertAndTraining,*array_LocalArea,*array_LocalLanguage,*array_LocalOtherPreferences,*array_LocalChildPreferences;
    NSIndexPath *index;
    
    //For edit contact info
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
    __weak IBOutlet SMUIScrollView *scrlViewMain;
    
    
}
@property (weak, nonatomic) IBOutlet UITableView *tbl_additionalInfo;
@property(strong,nonatomic)NSMutableArray *array_CertAndTraining,*array_Area,*array_Language,*array_OtherPreferences,*array_ChildPreferences,*array_specialNeeds;
@property(strong,nonatomic)NSMutableArray *array_IndexTag,*array_IndexBtnTag;
@property(strong,nonatomic)UIButton *btnSpecialNeed;

@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic,strong)SitterAdditionInformation *sitterAdditionalInfo;
@property(nonatomic,strong)NSMutableDictionary *dictUserInfo;
@property(nonatomic,strong)NSData *imgData;
@property(nonatomic,strong)NSMutableDictionary *dict_ArrayData;
@property (strong,nonatomic) NSArray *array_specialNeed;
@property (strong,nonatomic) NIDropDown *dropDownSpecialNeed;

-(IBAction)onValueChangeSwCertificate:(UISwitch*)sender;
-(IBAction)onClickedCertificateCalender:(UIButton*)sender;
- (IBAction)onClickedDateDonebutton:(id)sender;
- (IBAction)onClickedDateCancelbutton:(id)sender;

@end
